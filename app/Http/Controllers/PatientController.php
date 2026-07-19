<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterPatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\PatientMatcher;
use App\Services\PatientMergeService;
use App\Services\PatientRegistrationService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Patient registry.
 *
 * Two behaviours here are deliberate departures from the previous version:
 *
 *  - destroy() DEACTIVATES rather than deletes. A patient record that existed
 *    must stay resolvable: printed cards, referral letters and the audit trail
 *    all point at it.
 *  - Exceptions are not echoed back to the client. The old handler returned
 *    $e->getMessage() in the response body, which leaks schema details and
 *    occasionally patient data to whoever triggered the error.
 */
class PatientController extends Controller
{
    public function __construct(
        private readonly PatientRegistrationService $registration,
        private readonly PatientMatcher $matcher,
        private readonly PatientMergeService $merge,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        $patients = Patient::query()
            ->with('person')
            ->where('is_active', true)
            ->orderByDesc('registered_at')
            ->paginate($perPage);

        return response()->json([
            'data' => PatientResource::collection($patients->items()),
            'meta' => [
                'current_page' => $patients->currentPage(),
                'last_page' => $patients->lastPage(),
                'per_page' => $patients->perPage(),
                'total' => $patients->total(),
            ],
        ]);
    }

    /**
     * SEARCH BEFORE CREATE.
     *
     * This endpoint exists so the registration UI can force a lookup before it
     * offers "create new patient". A duplicate record splits a chart: the
     * allergy recorded last month becomes invisible today.
     */
    public function search(Request $request, string $term): JsonResponse
    {
        $results = $this->matcher->search($term);

        return response()->json([
            'data' => $results->map(fn ($r) => [
                'id' => $r->id,
                'mrn' => $r->mrn,
                'name_local' => $r->name_local,
                'name_latin' => $r->name_latin,
                'father_name' => $r->father_name,
                'gender' => $r->gender,
                'birth_date' => $r->birth_date,
                'village' => $r->village,
                'score' => round((float) $r->score, 3),
                'matched_on' => $r->matched_on,
            ]),
        ]);
    }

    /**
     * Score a prospective registration against existing records WITHOUT
     * creating anything, so the UI can warn before the clerk commits.
     */
    public function checkDuplicates(Request $request): JsonResponse
    {
        $candidates = $this->matcher->findDuplicates($request->all());

        return response()->json([
            'data' => $candidates,
            'has_strong_match' => $candidates->contains('confidence', 'strong'),
        ]);
    }

    public function store(RegisterPatientRequest $request): JsonResponse
    {
        $data = $request->validated();

        // A strong duplicate match blocks the create unless the clerk has
        // explicitly acknowledged it. A passive warning gets clicked through
        // under time pressure; a hard block with no override gets worked around
        // by inventing a slightly different name.
        if (! ($data['acknowledged_duplicates'] ?? false)) {
            $candidates = $this->matcher->findDuplicates($data);

            if ($candidates->contains('confidence', 'strong')) {
                return response()->json([
                    'message' => 'A very similar patient record already exists. '
                        .'Open it, or confirm this is a different person.',
                    'duplicates' => $candidates,
                ], 409);
            }
        }

        $patient = $this->registration->register($data);

        return (new PatientResource($patient))->response()->setStatusCode(201);
    }

    public function show(string $id): JsonResponse
    {
        $patient = Patient::with(['person', 'identifiers', 'contacts', 'companions'])
            ->findOrFail($id);

        // Follow merge links, so an old MRN on a printed card still opens the
        // surviving chart.
        $effective = $patient->effective();

        if ($effective->id !== $patient->id) {
            $effective->load(['person', 'identifiers', 'contacts', 'companions']);
        }

        // Reads are audited: no database trigger fires on a SELECT, and here
        // "who looked at this record" is the security event (ADR-010).
        AuditLogger::recordRead('patient', $effective->id, $effective->id);

        return response()->json([
            'data' => new PatientResource($effective),
            'merged_from' => $effective->id !== $patient->id ? $patient->id : null,
        ]);
    }

    public function update(RegisterPatientRequest $request, string $id): JsonResponse
    {
        $patient = Patient::with('person')->findOrFail($id);

        $updated = $this->registration->update($patient, $request->validated());

        return response()->json(['data' => new PatientResource($updated)]);
    }

    /** Deactivate, never delete. */
    public function destroy(string $id): JsonResponse
    {
        $patient = Patient::findOrFail($id);
        $patient->update(['is_active' => false]);

        AuditLogger::record('delete', 'patient', $patient->id, $patient->id);

        return response()->json(['message' => 'Patient deactivated.']);
    }

    /** Duplicate candidates awaiting human review. */
    public function duplicateQueue(): JsonResponse
    {
        $rows = DB::table('patient_duplicate_candidate as dc')
            ->join('patient as pa', 'pa.id', '=', 'dc.patient_a_id')
            ->join('person as na', 'na.id', '=', 'pa.id')
            ->join('patient as pb', 'pb.id', '=', 'dc.patient_b_id')
            ->join('person as nb', 'nb.id', '=', 'pb.id')
            ->where('dc.status', 'open')
            ->orderByDesc('dc.score')
            ->limit(100)
            ->get([
                'dc.id', 'dc.score', 'dc.matched_on', 'dc.detected_at',
                'pa.id as a_id', 'pa.mrn as a_mrn', 'na.name_local as a_name',
                'na.father_name as a_father', 'na.birth_date as a_birth_date',
                'pb.id as b_id', 'pb.mrn as b_mrn', 'nb.name_local as b_name',
                'nb.father_name as b_father', 'nb.birth_date as b_birth_date',
            ]);

        return response()->json(['data' => $rows]);
    }

    /**
     * Merge two records. NEVER automatic — a false positive fuses two people's
     * medical histories, which is worse than the duplicate it fixes.
     */
    public function mergePatients(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'survivor_id' => ['required', 'uuid', 'exists:patient,id'],
            'losing_id' => ['required', 'uuid', 'exists:patient,id', 'different:survivor_id'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
            'candidate_id' => ['nullable', 'uuid'],
        ]);

        $link = $this->merge->merge(
            $validated['survivor_id'],
            $validated['losing_id'],
            $validated['reason'],
        );

        if (! empty($validated['candidate_id'])) {
            DB::table('patient_duplicate_candidate')
                ->where('id', $validated['candidate_id'])
                ->update([
                    'status' => 'merged',
                    'reviewed_by' => $request->user()?->id,
                    'reviewed_at' => now(),
                ]);
        }

        return response()->json([
            'message' => 'Patients merged.',
            'link_id' => $link->id,
        ]);
    }

    /** Undo a merge. Every moved row is restored to its original owner. */
    public function unmergePatients(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'link_id' => ['required', 'uuid', 'exists:patient_link,id'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $this->merge->unmerge($validated['link_id'], $validated['reason']);

        return response()->json(['message' => 'Merge reversed.']);
    }

    /** Mark a duplicate candidate as reviewed and rejected. */
    public function rejectDuplicate(Request $request, string $candidateId): JsonResponse
    {
        DB::table('patient_duplicate_candidate')
            ->where('id', $candidateId)
            ->update([
                'status' => 'rejected',
                'reviewed_by' => $request->user()?->id,
                'reviewed_at' => now(),
            ]);

        return response()->json(['message' => 'Marked as not a duplicate.']);
    }
}

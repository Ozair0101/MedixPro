<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobGradeRequest;
use App\Http\Resources\JobGradeResource;
use App\Models\JobGrade;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `job_grade`.
 */
class JobGradeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = JobGrade::query()->paginate($perPage);

        return response()->json([
            'data' => JobGradeResource::collection($rows->items()),
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'per_page' => $rows->perPage(),
                'total' => $rows->total(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $jobGrade = JobGrade::findOrFail($id);

        return response()->json(['data' => new JobGradeResource($jobGrade)]);
    }

    public function store(StoreJobGradeRequest $request): JsonResponse
    {
        $jobGrade = JobGrade::create($request->validated());

        AuditLogger::record('create', 'job_grade', (string) $jobGrade->getKey());

        return response()->json(
            ['data' => new JobGradeResource($jobGrade)], 201
        );
    }

    public function update(StoreJobGradeRequest $request, string $id): JsonResponse
    {
        $jobGrade = JobGrade::findOrFail($id);
        $jobGrade->update($request->validated());

        AuditLogger::record('update', 'job_grade', $id);

        return response()->json(['data' => new JobGradeResource($jobGrade)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $jobGrade = JobGrade::findOrFail($id);

        $jobGrade->delete();

        AuditLogger::record('delete', 'job_grade', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

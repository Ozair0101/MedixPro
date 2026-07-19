<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCreditNoteRequest;
use App\Http\Resources\CreditNoteResource;
use App\Models\CreditNote;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `credit_note`.
 */
class CreditNoteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = CreditNote::query()->paginate($perPage);

        return response()->json([
            'data' => CreditNoteResource::collection($rows->items()),
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
        $creditNote = CreditNote::findOrFail($id);

        return response()->json(['data' => new CreditNoteResource($creditNote)]);
    }

    public function store(StoreCreditNoteRequest $request): JsonResponse
    {
        $creditNote = CreditNote::create($request->validated());

        AuditLogger::record('create', 'credit_note', (string) $creditNote->getKey());

        return response()->json(
            ['data' => new CreditNoteResource($creditNote)], 201
        );
    }

    public function update(StoreCreditNoteRequest $request, string $id): JsonResponse
    {
        $creditNote = CreditNote::findOrFail($id);
        $creditNote->update($request->validated());

        AuditLogger::record('update', 'credit_note', $id);

        return response()->json(['data' => new CreditNoteResource($creditNote)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $creditNote = CreditNote::findOrFail($id);

        $creditNote->delete();

        AuditLogger::record('delete', 'credit_note', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

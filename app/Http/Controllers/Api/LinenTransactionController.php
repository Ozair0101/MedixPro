<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLinenTransactionRequest;
use App\Http\Resources\LinenTransactionResource;
use App\Models\LinenTransaction;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `linen_transaction`.
 */
class LinenTransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LinenTransaction::query()->paginate($perPage);

        return response()->json([
            'data' => LinenTransactionResource::collection($rows->items()),
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
        $linenTransaction = LinenTransaction::findOrFail($id);

        return response()->json(['data' => new LinenTransactionResource($linenTransaction)]);
    }

    public function store(StoreLinenTransactionRequest $request): JsonResponse
    {
        $linenTransaction = LinenTransaction::create($request->validated());

        AuditLogger::record('create', 'linen_transaction', (string) $linenTransaction->getKey());

        return response()->json(
            ['data' => new LinenTransactionResource($linenTransaction)], 201
        );
    }

    public function update(StoreLinenTransactionRequest $request, string $id): JsonResponse
    {
        $linenTransaction = LinenTransaction::findOrFail($id);
        $linenTransaction->update($request->validated());

        AuditLogger::record('update', 'linen_transaction', $id);

        return response()->json(['data' => new LinenTransactionResource($linenTransaction)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $linenTransaction = LinenTransaction::findOrFail($id);

        $linenTransaction->delete();

        AuditLogger::record('delete', 'linen_transaction', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayerRequest;
use App\Http\Resources\PayerResource;
use App\Models\Payer;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `payer`.
 */
class PayerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Payer::query()->paginate($perPage);

        return response()->json([
            'data' => PayerResource::collection($rows->items()),
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
        $payer = Payer::findOrFail($id);

        return response()->json(['data' => new PayerResource($payer)]);
    }

    public function store(StorePayerRequest $request): JsonResponse
    {
        $payer = Payer::create($request->validated());

        AuditLogger::record('create', 'payer', (string) $payer->getKey());

        return response()->json(
            ['data' => new PayerResource($payer)], 201
        );
    }

    public function update(StorePayerRequest $request, string $id): JsonResponse
    {
        $payer = Payer::findOrFail($id);
        $payer->update($request->validated());

        AuditLogger::record('update', 'payer', $id);

        return response()->json(['data' => new PayerResource($payer)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $payer = Payer::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $payer->update(['is_active' => false]);

        AuditLogger::record('delete', 'payer', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}

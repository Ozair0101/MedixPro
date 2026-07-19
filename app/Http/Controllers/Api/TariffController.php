<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTariffRequest;
use App\Http\Resources\TariffResource;
use App\Models\Tariff;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `tariff`.
 */
class TariffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Tariff::query()->paginate($perPage);

        return response()->json([
            'data' => TariffResource::collection($rows->items()),
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
        $tariff = Tariff::findOrFail($id);

        return response()->json(['data' => new TariffResource($tariff)]);
    }

    public function store(StoreTariffRequest $request): JsonResponse
    {
        $tariff = Tariff::create($request->validated());

        AuditLogger::record('create', 'tariff', (string) $tariff->getKey());

        return response()->json(
            ['data' => new TariffResource($tariff)], 201
        );
    }

    public function update(StoreTariffRequest $request, string $id): JsonResponse
    {
        $tariff = Tariff::findOrFail($id);
        $tariff->update($request->validated());

        AuditLogger::record('update', 'tariff', $id);

        return response()->json(['data' => new TariffResource($tariff)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $tariff = Tariff::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $tariff->update(['is_active' => false]);

        AuditLogger::record('delete', 'tariff', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}

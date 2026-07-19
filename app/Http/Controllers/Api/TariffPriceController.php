<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTariffPriceRequest;
use App\Http\Resources\TariffPriceResource;
use App\Models\TariffPrice;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `tariff_price`.
 */
class TariffPriceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = TariffPrice::query()->paginate($perPage);

        return response()->json([
            'data' => TariffPriceResource::collection($rows->items()),
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
        $tariffPrice = TariffPrice::findOrFail($id);

        return response()->json(['data' => new TariffPriceResource($tariffPrice)]);
    }

    public function store(StoreTariffPriceRequest $request): JsonResponse
    {
        $tariffPrice = TariffPrice::create($request->validated());

        AuditLogger::record('create', 'tariff_price', (string) $tariffPrice->getKey());

        return response()->json(
            ['data' => new TariffPriceResource($tariffPrice)], 201
        );
    }

    public function update(StoreTariffPriceRequest $request, string $id): JsonResponse
    {
        $tariffPrice = TariffPrice::findOrFail($id);
        $tariffPrice->update($request->validated());

        AuditLogger::record('update', 'tariff_price', $id);

        return response()->json(['data' => new TariffPriceResource($tariffPrice)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $tariffPrice = TariffPrice::findOrFail($id);

        $tariffPrice->delete();

        AuditLogger::record('delete', 'tariff_price', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

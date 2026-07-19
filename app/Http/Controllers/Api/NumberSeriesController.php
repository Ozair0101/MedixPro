<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNumberSeriesRequest;
use App\Http\Resources\NumberSeriesResource;
use App\Models\NumberSeries;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `number_series`.
 */
class NumberSeriesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = NumberSeries::query()->paginate($perPage);

        return response()->json([
            'data' => NumberSeriesResource::collection($rows->items()),
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
        $numberSeries = NumberSeries::findOrFail($id);

        return response()->json(['data' => new NumberSeriesResource($numberSeries)]);
    }

    public function store(StoreNumberSeriesRequest $request): JsonResponse
    {
        $numberSeries = NumberSeries::create($request->validated());

        AuditLogger::record('create', 'number_series', (string) $numberSeries->getKey());

        return response()->json(
            ['data' => new NumberSeriesResource($numberSeries)], 201
        );
    }

    public function update(StoreNumberSeriesRequest $request, string $id): JsonResponse
    {
        $numberSeries = NumberSeries::findOrFail($id);
        $numberSeries->update($request->validated());

        AuditLogger::record('update', 'number_series', $id);

        return response()->json(['data' => new NumberSeriesResource($numberSeries)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $numberSeries = NumberSeries::findOrFail($id);

        $numberSeries->delete();

        AuditLogger::record('delete', 'number_series', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

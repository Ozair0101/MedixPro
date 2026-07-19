<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFiscalYearRequest;
use App\Http\Resources\FiscalYearResource;
use App\Models\FiscalYear;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `fiscal_year`.
 */
class FiscalYearController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = FiscalYear::query()->paginate($perPage);

        return response()->json([
            'data' => FiscalYearResource::collection($rows->items()),
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
        $fiscalYear = FiscalYear::findOrFail($id);

        return response()->json(['data' => new FiscalYearResource($fiscalYear)]);
    }

    public function store(StoreFiscalYearRequest $request): JsonResponse
    {
        $fiscalYear = FiscalYear::create($request->validated());

        AuditLogger::record('create', 'fiscal_year', (string) $fiscalYear->getKey());

        return response()->json(
            ['data' => new FiscalYearResource($fiscalYear)], 201
        );
    }

    public function update(StoreFiscalYearRequest $request, string $id): JsonResponse
    {
        $fiscalYear = FiscalYear::findOrFail($id);
        $fiscalYear->update($request->validated());

        AuditLogger::record('update', 'fiscal_year', $id);

        return response()->json(['data' => new FiscalYearResource($fiscalYear)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $fiscalYear = FiscalYear::findOrFail($id);

        $fiscalYear->delete();

        AuditLogger::record('delete', 'fiscal_year', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

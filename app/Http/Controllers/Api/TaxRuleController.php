<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaxRuleRequest;
use App\Http\Resources\TaxRuleResource;
use App\Models\TaxRule;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `tax_rule`.
 */
class TaxRuleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = TaxRule::query()->paginate($perPage);

        return response()->json([
            'data' => TaxRuleResource::collection($rows->items()),
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
        $taxRule = TaxRule::findOrFail($id);

        return response()->json(['data' => new TaxRuleResource($taxRule)]);
    }

    public function store(StoreTaxRuleRequest $request): JsonResponse
    {
        $taxRule = TaxRule::create($request->validated());

        AuditLogger::record('create', 'tax_rule', (string) $taxRule->getKey());

        return response()->json(
            ['data' => new TaxRuleResource($taxRule)], 201
        );
    }

    public function update(StoreTaxRuleRequest $request, string $id): JsonResponse
    {
        $taxRule = TaxRule::findOrFail($id);
        $taxRule->update($request->validated());

        AuditLogger::record('update', 'tax_rule', $id);

        return response()->json(['data' => new TaxRuleResource($taxRule)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $taxRule = TaxRule::findOrFail($id);

        $taxRule->delete();

        AuditLogger::record('delete', 'tax_rule', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

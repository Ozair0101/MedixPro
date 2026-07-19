<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabReflexRuleRequest;
use App\Http\Resources\LabReflexRuleResource;
use App\Models\LabReflexRule;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `lab_reflex_rule`.
 */
class LabReflexRuleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LabReflexRule::query()->paginate($perPage);

        return response()->json([
            'data' => LabReflexRuleResource::collection($rows->items()),
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
        $labReflexRule = LabReflexRule::findOrFail($id);

        return response()->json(['data' => new LabReflexRuleResource($labReflexRule)]);
    }

    public function store(StoreLabReflexRuleRequest $request): JsonResponse
    {
        $labReflexRule = LabReflexRule::create($request->validated());

        AuditLogger::record('create', 'lab_reflex_rule', (string) $labReflexRule->getKey());

        return response()->json(
            ['data' => new LabReflexRuleResource($labReflexRule)], 201
        );
    }

    public function update(StoreLabReflexRuleRequest $request, string $id): JsonResponse
    {
        $labReflexRule = LabReflexRule::findOrFail($id);
        $labReflexRule->update($request->validated());

        AuditLogger::record('update', 'lab_reflex_rule', $id);

        return response()->json(['data' => new LabReflexRuleResource($labReflexRule)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $labReflexRule = LabReflexRule::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $labReflexRule->update(['is_active' => false]);

        AuditLogger::record('delete', 'lab_reflex_rule', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}

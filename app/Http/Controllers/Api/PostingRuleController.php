<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostingRuleRequest;
use App\Http\Resources\PostingRuleResource;
use App\Models\PostingRule;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `posting_rule`.
 */
class PostingRuleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PostingRule::query()->paginate($perPage);

        return response()->json([
            'data' => PostingRuleResource::collection($rows->items()),
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
        $postingRule = PostingRule::findOrFail($id);

        return response()->json(['data' => new PostingRuleResource($postingRule)]);
    }

    public function store(StorePostingRuleRequest $request): JsonResponse
    {
        $postingRule = PostingRule::create($request->validated());

        AuditLogger::record('create', 'posting_rule', (string) $postingRule->getKey());

        return response()->json(
            ['data' => new PostingRuleResource($postingRule)], 201
        );
    }

    public function update(StorePostingRuleRequest $request, string $id): JsonResponse
    {
        $postingRule = PostingRule::findOrFail($id);
        $postingRule->update($request->validated());

        AuditLogger::record('update', 'posting_rule', $id);

        return response()->json(['data' => new PostingRuleResource($postingRule)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $postingRule = PostingRule::findOrFail($id);

        $postingRule->delete();

        AuditLogger::record('delete', 'posting_rule', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

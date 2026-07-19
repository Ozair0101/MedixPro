<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountCoverageRequest;
use App\Http\Resources\AccountCoverageResource;
use App\Models\AccountCoverage;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `account_coverage`.
 */
class AccountCoverageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = AccountCoverage::query()->paginate($perPage);

        return response()->json([
            'data' => AccountCoverageResource::collection($rows->items()),
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
        $accountCoverage = AccountCoverage::findOrFail($id);

        return response()->json(['data' => new AccountCoverageResource($accountCoverage)]);
    }

    public function store(StoreAccountCoverageRequest $request): JsonResponse
    {
        $accountCoverage = AccountCoverage::create($request->validated());

        AuditLogger::record('create', 'account_coverage', (string) $accountCoverage->getKey());

        return response()->json(
            ['data' => new AccountCoverageResource($accountCoverage)], 201
        );
    }

    public function update(StoreAccountCoverageRequest $request, string $id): JsonResponse
    {
        $accountCoverage = AccountCoverage::findOrFail($id);
        $accountCoverage->update($request->validated());

        AuditLogger::record('update', 'account_coverage', $id);

        return response()->json(['data' => new AccountCoverageResource($accountCoverage)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $accountCoverage = AccountCoverage::findOrFail($id);

        $accountCoverage->delete();

        AuditLogger::record('delete', 'account_coverage', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShamsiMonthRequest;
use App\Http\Resources\ShamsiMonthResource;
use App\Models\ShamsiMonth;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `shamsi_month`.
 */
class ShamsiMonthController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ShamsiMonth::query()->paginate($perPage);

        return response()->json([
            'data' => ShamsiMonthResource::collection($rows->items()),
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
        $shamsiMonth = ShamsiMonth::findOrFail($id);

        return response()->json(['data' => new ShamsiMonthResource($shamsiMonth)]);
    }

    public function store(StoreShamsiMonthRequest $request): JsonResponse
    {
        $shamsiMonth = ShamsiMonth::create($request->validated());

        AuditLogger::record('create', 'shamsi_month', (string) $shamsiMonth->getKey());

        return response()->json(
            ['data' => new ShamsiMonthResource($shamsiMonth)], 201
        );
    }

    public function update(StoreShamsiMonthRequest $request, string $id): JsonResponse
    {
        $shamsiMonth = ShamsiMonth::findOrFail($id);
        $shamsiMonth->update($request->validated());

        AuditLogger::record('update', 'shamsi_month', $id);

        return response()->json(['data' => new ShamsiMonthResource($shamsiMonth)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $shamsiMonth = ShamsiMonth::findOrFail($id);

        $shamsiMonth->delete();

        AuditLogger::record('delete', 'shamsi_month', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

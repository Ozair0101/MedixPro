<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMealServiceRequest;
use App\Http\Resources\MealServiceResource;
use App\Models\MealService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `meal_service`.
 */
class MealServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = MealService::query()->paginate($perPage);

        return response()->json([
            'data' => MealServiceResource::collection($rows->items()),
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
        $mealService = MealService::findOrFail($id);

        return response()->json(['data' => new MealServiceResource($mealService)]);
    }

    public function store(StoreMealServiceRequest $request): JsonResponse
    {
        $mealService = MealService::create($request->validated());

        AuditLogger::record('create', 'meal_service', (string) $mealService->getKey());

        return response()->json(
            ['data' => new MealServiceResource($mealService)], 201
        );
    }

    public function update(StoreMealServiceRequest $request, string $id): JsonResponse
    {
        $mealService = MealService::findOrFail($id);
        $mealService->update($request->validated());

        AuditLogger::record('update', 'meal_service', $id);

        return response()->json(['data' => new MealServiceResource($mealService)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $mealService = MealService::findOrFail($id);

        $mealService->delete();

        AuditLogger::record('delete', 'meal_service', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

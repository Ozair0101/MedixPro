<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppSettingRequest;
use App\Http\Resources\AppSettingResource;
use App\Models\AppSetting;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `app_setting`.
 */
class AppSettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = AppSetting::query()->paginate($perPage);

        return response()->json([
            'data' => AppSettingResource::collection($rows->items()),
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
        $appSetting = AppSetting::findOrFail($id);

        return response()->json(['data' => new AppSettingResource($appSetting)]);
    }

    public function store(StoreAppSettingRequest $request): JsonResponse
    {
        $appSetting = AppSetting::create($request->validated());

        AuditLogger::record('create', 'app_setting', (string) $appSetting->getKey());

        return response()->json(
            ['data' => new AppSettingResource($appSetting)], 201
        );
    }

    public function update(StoreAppSettingRequest $request, string $id): JsonResponse
    {
        $appSetting = AppSetting::findOrFail($id);
        $appSetting->update($request->validated());

        AuditLogger::record('update', 'app_setting', $id);

        return response()->json(['data' => new AppSettingResource($appSetting)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $appSetting = AppSetting::findOrFail($id);

        $appSetting->delete();

        AuditLogger::record('delete', 'app_setting', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

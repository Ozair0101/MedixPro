<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoseFormRequest;
use App\Http\Resources\DoseFormResource;
use App\Models\DoseForm;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `dose_form`.
 */
class DoseFormController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = DoseForm::query()->paginate($perPage);

        return response()->json([
            'data' => DoseFormResource::collection($rows->items()),
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
        $doseForm = DoseForm::findOrFail($id);

        return response()->json(['data' => new DoseFormResource($doseForm)]);
    }

    public function store(StoreDoseFormRequest $request): JsonResponse
    {
        $doseForm = DoseForm::create($request->validated());

        AuditLogger::record('create', 'dose_form', (string) $doseForm->getKey());

        return response()->json(
            ['data' => new DoseFormResource($doseForm)], 201
        );
    }

    public function update(StoreDoseFormRequest $request, string $id): JsonResponse
    {
        $doseForm = DoseForm::findOrFail($id);
        $doseForm->update($request->validated());

        AuditLogger::record('update', 'dose_form', $id);

        return response()->json(['data' => new DoseFormResource($doseForm)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $doseForm = DoseForm::findOrFail($id);

        $doseForm->delete();

        AuditLogger::record('delete', 'dose_form', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreElectronicSignatureRequest;
use App\Http\Resources\ElectronicSignatureResource;
use App\Models\ElectronicSignature;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `electronic_signature`.
 *
 * READ-ONLY. This table is append-only at the database level; the write
 * endpoints are omitted rather than offered and rejected.
 */
class ElectronicSignatureController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ElectronicSignature::query()->paginate($perPage);

        return response()->json([
            'data' => ElectronicSignatureResource::collection($rows->items()),
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
        $electronicSignature = ElectronicSignature::findOrFail($id);

        return response()->json(['data' => new ElectronicSignatureResource($electronicSignature)]);
    }
}

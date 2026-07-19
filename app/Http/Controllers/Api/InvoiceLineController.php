<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceLineRequest;
use App\Http\Resources\InvoiceLineResource;
use App\Models\InvoiceLine;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `invoice_line`.
 */
class InvoiceLineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = InvoiceLine::query()->paginate($perPage);

        return response()->json([
            'data' => InvoiceLineResource::collection($rows->items()),
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
        $invoiceLine = InvoiceLine::findOrFail($id);

        return response()->json(['data' => new InvoiceLineResource($invoiceLine)]);
    }

    public function store(StoreInvoiceLineRequest $request): JsonResponse
    {
        $invoiceLine = InvoiceLine::create($request->validated());

        AuditLogger::record('create', 'invoice_line', (string) $invoiceLine->getKey());

        return response()->json(
            ['data' => new InvoiceLineResource($invoiceLine)], 201
        );
    }

    public function update(StoreInvoiceLineRequest $request, string $id): JsonResponse
    {
        $invoiceLine = InvoiceLine::findOrFail($id);
        $invoiceLine->update($request->validated());

        AuditLogger::record('update', 'invoice_line', $id);

        return response()->json(['data' => new InvoiceLineResource($invoiceLine)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $invoiceLine = InvoiceLine::findOrFail($id);

        $invoiceLine->delete();

        AuditLogger::record('delete', 'invoice_line', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

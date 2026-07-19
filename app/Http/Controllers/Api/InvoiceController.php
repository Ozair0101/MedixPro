<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `invoice`.
 */
class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Invoice::query()->paginate($perPage);

        return response()->json([
            'data' => InvoiceResource::collection($rows->items()),
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
        $invoice = Invoice::findOrFail($id);

        return response()->json(['data' => new InvoiceResource($invoice)]);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = Invoice::create($request->validated());

        AuditLogger::record('create', 'invoice', (string) $invoice->getKey());

        return response()->json(
            ['data' => new InvoiceResource($invoice)], 201
        );
    }

    public function update(StoreInvoiceRequest $request, string $id): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->validated());

        AuditLogger::record('update', 'invoice', $id);

        return response()->json(['data' => new InvoiceResource($invoice)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->delete();

        AuditLogger::record('delete', 'invoice', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

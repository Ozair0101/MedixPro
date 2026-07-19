<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentAllocationRequest;
use App\Http\Resources\PaymentAllocationResource;
use App\Models\PaymentAllocation;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `payment_allocation`.
 */
class PaymentAllocationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PaymentAllocation::query()->paginate($perPage);

        return response()->json([
            'data' => PaymentAllocationResource::collection($rows->items()),
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
        $paymentAllocation = PaymentAllocation::findOrFail($id);

        return response()->json(['data' => new PaymentAllocationResource($paymentAllocation)]);
    }

    public function store(StorePaymentAllocationRequest $request): JsonResponse
    {
        $paymentAllocation = PaymentAllocation::create($request->validated());

        AuditLogger::record('create', 'payment_allocation', (string) $paymentAllocation->getKey());

        return response()->json(
            ['data' => new PaymentAllocationResource($paymentAllocation)], 201
        );
    }

    public function update(StorePaymentAllocationRequest $request, string $id): JsonResponse
    {
        $paymentAllocation = PaymentAllocation::findOrFail($id);
        $paymentAllocation->update($request->validated());

        AuditLogger::record('update', 'payment_allocation', $id);

        return response()->json(['data' => new PaymentAllocationResource($paymentAllocation)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $paymentAllocation = PaymentAllocation::findOrFail($id);

        $paymentAllocation->delete();

        AuditLogger::record('delete', 'payment_allocation', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

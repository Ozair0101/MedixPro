<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `payment`.
 */
class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Payment::query()->paginate($perPage);

        return response()->json([
            'data' => PaymentResource::collection($rows->items()),
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
        $payment = Payment::findOrFail($id);

        return response()->json(['data' => new PaymentResource($payment)]);
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $payment = Payment::create($request->validated());

        AuditLogger::record('create', 'payment', (string) $payment->getKey());

        return response()->json(
            ['data' => new PaymentResource($payment)], 201
        );
    }

    public function update(StorePaymentRequest $request, string $id): JsonResponse
    {
        $payment = Payment::findOrFail($id);
        $payment->update($request->validated());

        AuditLogger::record('update', 'payment', $id);

        return response()->json(['data' => new PaymentResource($payment)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $payment = Payment::findOrFail($id);

        $payment->delete();

        AuditLogger::record('delete', 'payment', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

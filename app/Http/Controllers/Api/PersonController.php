<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePersonRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `person`.
 */
class PersonController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Person::query()->paginate($perPage);

        return response()->json([
            'data' => PersonResource::collection($rows->items()),
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
        $person = Person::findOrFail($id);

        return response()->json(['data' => new PersonResource($person)]);
    }

    public function store(StorePersonRequest $request): JsonResponse
    {
        $person = Person::create($request->validated());

        AuditLogger::record('create', 'person', (string) $person->getKey());

        return response()->json(
            ['data' => new PersonResource($person)], 201
        );
    }

    public function update(StorePersonRequest $request, string $id): JsonResponse
    {
        $person = Person::findOrFail($id);
        $person->update($request->validated());

        AuditLogger::record('update', 'person', $id);

        return response()->json(['data' => new PersonResource($person)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $person = Person::findOrFail($id);

        $person->delete();

        AuditLogger::record('delete', 'person', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}

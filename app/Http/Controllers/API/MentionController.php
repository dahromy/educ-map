<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreMentionRequest;
use App\Http\Requests\API\UpdateMentionRequest;
use App\Http\Resources\API\MentionResource;
use App\Models\Mention;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Mentions",
 *     description="Endpoints for accessing and managing academic mentions/specializations"
 * )
 */
class MentionController extends Controller
{
    /**
     * Display a listing of the mentions.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     *
     * @OA\Get(
     *     path="/api/mentions",
     *     summary="Get a list of all mentions",
     *     tags={"Mentions"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by mention name (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by (name, created_at)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"name", "created_at"}, default="name")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction (asc or desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of mentions",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MentionResource"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Mention::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        $allowedSortFields = ['name', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $mentions = $query->get();

        return MentionResource::collection($mentions);
    }

    /**
     * Store a newly created mention in storage.
     *
     * @param StoreMentionRequest $request
     * @return MentionResource
     *
     * @OA\Post(
     *     path="/api/mentions",
     *     summary="Create a new mention",
     *     tags={"Mentions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="description", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Mention created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/MentionResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreMentionRequest $request): MentionResource
    {
        $mention = Mention::create($request->validated());

        return new MentionResource($mention);
    }

    /**
     * Display the specified mention.
     *
     * @param Mention $mention
     * @return MentionResource
     *
     * @OA\Get(
     *     path="/api/mentions/{id}",
     *     summary="Get a specific mention",
     *     tags={"Mentions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the mention to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mention details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/MentionResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Mention not found")
     * )
     */
    public function show(Mention $mention): MentionResource
    {
        return new MentionResource($mention);
    }

    /**
     * Update the specified mention in storage.
     *
     * @param UpdateMentionRequest $request
     * @param Mention $mention
     * @return MentionResource
     *
     * @OA\Put(
     *     path="/api/mentions/{id}",
     *     summary="Update a mention",
     *     tags={"Mentions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the mention to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="description", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mention updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/MentionResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Mention not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateMentionRequest $request, Mention $mention): MentionResource
    {
        $mention->update($request->validated());

        return new MentionResource($mention);
    }

    /**
     * Remove the specified mention from storage.
     *
     * @param Mention $mention
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/api/mentions/{id}",
     *     summary="Delete a mention",
     *     tags={"Mentions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the mention to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mention deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Mention deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Mention not found"),
     *     @OA\Response(response=409, description="Conflict - Mention has associated program offerings")
     * )
     */
    public function destroy(Mention $mention): JsonResponse
    {
        // Check if mention has associated program offerings
        if ($mention->programOfferings()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete mention. It has associated program offerings.'
            ], 409);
        }

        $mention->delete();

        return response()->json([
            'message' => 'Mention deleted successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreLabelRequest;
use App\Http\Requests\API\UpdateLabelRequest;
use App\Http\Resources\API\LabelResource;
use App\Models\Label;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Labels",
 *     description="Endpoints for accessing and managing establishment labels"
 * )
 */
class LabelController extends Controller
{
    /**
     * Display a listing of the labels.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     *
     * @OA\Get(
     *     path="/api/labels",
     *     summary="Get a list of all labels",
     *     tags={"Labels"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by label name (partial match)",
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
     *         description="List of labels",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/LabelResource"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Label::query();

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

        $labels = $query->get();

        return LabelResource::collection($labels);
    }

    /**
     * Store a newly created label in storage.
     *
     * @param StoreLabelRequest $request
     * @return LabelResource
     *
     * @OA\Post(
     *     path="/api/labels",
     *     summary="Create a new label",
     *     tags={"Labels"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="color", type="string", pattern="^#[0-9A-Fa-f]{6}$", example="#FF5733"),
     *             @OA\Property(property="description", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Label created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/LabelResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreLabelRequest $request): LabelResource
    {
        $label = Label::create($request->validated());

        return new LabelResource($label);
    }

    /**
     * Display the specified label.
     *
     * @param Label $label
     * @return LabelResource
     *
     * @OA\Get(
     *     path="/api/labels/{id}",
     *     summary="Get a specific label",
     *     tags={"Labels"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the label to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Label details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/LabelResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Label not found")
     * )
     */
    public function show(Label $label): LabelResource
    {
        return new LabelResource($label);
    }

    /**
     * Update the specified label in storage.
     *
     * @param UpdateLabelRequest $request
     * @param Label $label
     * @return LabelResource
     *
     * @OA\Put(
     *     path="/api/labels/{id}",
     *     summary="Update a label",
     *     tags={"Labels"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the label to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="color", type="string", pattern="^#[0-9A-Fa-f]{6}$", example="#FF5733"),
     *             @OA\Property(property="description", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Label updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/LabelResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Label not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateLabelRequest $request, Label $label): LabelResource
    {
        $label->update($request->validated());

        return new LabelResource($label);
    }

    /**
     * Remove the specified label from storage.
     *
     * @param Label $label
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/api/labels/{id}",
     *     summary="Delete a label",
     *     tags={"Labels"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the label to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Label deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Label deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Label not found"),
     *     @OA\Response(response=409, description="Conflict - Label has associated establishments")
     * )
     */
    public function destroy(Label $label): JsonResponse
    {
        // Check if label has associated establishments
        if ($label->establishments()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete label. It has associated establishments.'
            ], 409);
        }

        $label->delete();

        return response()->json([
            'message' => 'Label deleted successfully'
        ]);
    }
}

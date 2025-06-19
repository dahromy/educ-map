<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreGradeRequest;
use App\Http\Requests\API\UpdateGradeRequest;
use App\Http\Resources\API\GradeResource;
use App\Models\Grade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Grades",
 *     description="Endpoints for accessing and managing academic grades/levels"
 * )
 */
class GradeController extends Controller
{
    /**
     * Display a listing of the grades.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     *
     * @OA\Get(
     *     path="/api/grades",
     *     summary="Get a list of all grades",
     *     tags={"Grades"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by grade name (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by (name, level, created_at)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"name", "level", "created_at"}, default="level")
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
     *         description="List of grades",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/GradeResource"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Grade::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'level');
        $sortDirection = $request->get('sort_direction', 'asc');

        $allowedSortFields = ['name', 'level', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $grades = $query->get();

        return GradeResource::collection($grades);
    }

    /**
     * Store a newly created grade in storage.
     *
     * @param StoreGradeRequest $request
     * @return GradeResource
     *
     * @OA\Post(
     *     path="/api/grades",
     *     summary="Create a new grade",
     *     tags={"Grades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="level", type="integer", minimum=1, maximum=10, nullable=true),
     *             @OA\Property(property="description", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Grade created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/GradeResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreGradeRequest $request): GradeResource
    {
        $grade = Grade::create($request->validated());

        return new GradeResource($grade);
    }

    /**
     * Display the specified grade.
     *
     * @param Grade $grade
     * @return GradeResource
     *
     * @OA\Get(
     *     path="/api/grades/{id}",
     *     summary="Get a specific grade",
     *     tags={"Grades"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the grade to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Grade details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/GradeResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Grade not found")
     * )
     */
    public function show(Grade $grade): GradeResource
    {
        return new GradeResource($grade);
    }

    /**
     * Update the specified grade in storage.
     *
     * @param UpdateGradeRequest $request
     * @param Grade $grade
     * @return GradeResource
     *
     * @OA\Put(
     *     path="/api/grades/{id}",
     *     summary="Update a grade",
     *     tags={"Grades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the grade to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="level", type="integer", minimum=1, maximum=10, nullable=true),
     *             @OA\Property(property="description", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Grade updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/GradeResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Grade not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateGradeRequest $request, Grade $grade): GradeResource
    {
        $grade->update($request->validated());

        return new GradeResource($grade);
    }

    /**
     * Remove the specified grade from storage.
     *
     * @param Grade $grade
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/api/grades/{id}",
     *     summary="Delete a grade",
     *     tags={"Grades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the grade to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Grade deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Grade deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Grade not found"),
     *     @OA\Response(response=409, description="Conflict - Grade has associated program offerings")
     * )
     */
    public function destroy(Grade $grade): JsonResponse
    {
        // Check if grade has associated program offerings
        if ($grade->programOfferings()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete grade. It has associated program offerings.'
            ], 409);
        }

        $grade->delete();

        return response()->json([
            'message' => 'Grade deleted successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SearchHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Search History",
 *     description="Endpoints for managing user search history"
 * )
 */
class SearchHistoryController extends Controller
{
    /**
     * Retrieve saved searches for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/me/searches",
     *     summary="Get the authenticated user's saved searches",
     *     tags={"Search History"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of saved searches",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="search_query_text", type="string", nullable=true),
     *                 @OA\Property(property="search_filters", type="object", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $searches = SearchHistory::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($searches);
    }

    /**
     * Store a newly created search history in storage.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/me/searches",
     *     summary="Save a search query and filters for the authenticated user",
     *     tags={"Search History"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="search_query_text", type="string", example="Computer Science Programs"),
     *             @OA\Property(
     *                 property="search_filters",
     *                 type="object",
     *                 example={"region": "Analamanga", "domain": "Computer Science"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Search saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Search saved successfully"),
     *             @OA\Property(property="search", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="search_query_text", type="string", nullable=true),
     *                 @OA\Property(property="search_filters", type="object", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search_query_text' => 'nullable|string|max:255',
            'search_filters' => 'nullable|array',
        ]);

        $searchHistory = SearchHistory::create([
            'user_id' => $request->user()->id,
            'search_query_text' => $validated['search_query_text'] ?? null,
            'search_filters' => $validated['search_filters'] ?? null,
        ]);

        return response()->json([
            'message' => 'Search saved successfully',
            'search' => $searchHistory
        ], 201);
    }

    /**
     * Delete the specified search history.
     *
     * @param SearchHistory $search
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/api/me/searches/{id}",
     *     summary="Delete a saved search for the authenticated user",
     *     tags={"Search History"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the saved search to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Search deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - not owner of this search"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Search not found"
     *     )
     * )
     */
    public function destroy(SearchHistory $search, Request $request): JsonResponse
    {
        // Verify ownership
        if ($search->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $search->delete();

        return response()->json([
            'message' => 'Search deleted successfully'
        ]);
    }
}

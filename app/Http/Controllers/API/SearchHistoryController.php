<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SearchHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchHistoryController extends Controller
{
    /**
     * Retrieve saved searches for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
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

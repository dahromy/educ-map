<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Lists",
 *     description="Endpoints for accessing simple list data for filters and forms"
 * )
 */
class ListController extends Controller
{
    /**
     * Get a summary of all available list endpoints.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/lists",
     *     summary="Get information about all available list endpoints",
     *     tags={"Lists"},
     *     @OA\Response(
     *         response=200,
     *         description="Summary of available list endpoints",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="endpoints",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="name", type="string", example="categories"),
     *                         @OA\Property(property="url", type="string", example="/api/categories"),
     *                         @OA\Property(property="description", type="string", example="Get list of educational institution categories")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $endpoints = [
            [
                'name' => 'categories',
                'url' => '/api/categories',
                'description' => 'Get list of educational institution categories'
            ],
            [
                'name' => 'domains',
                'url' => '/api/domains',
                'description' => 'Get list of study domains/fields'
            ],
            [
                'name' => 'grades',
                'url' => '/api/grades',
                'description' => 'Get list of academic grades/levels'
            ],
            [
                'name' => 'mentions',
                'url' => '/api/mentions',
                'description' => 'Get list of program mentions/specializations'
            ],
            [
                'name' => 'labels',
                'url' => '/api/labels',
                'description' => 'Get list of establishment labels/tags'
            ]
        ];

        return response()->json([
            'data' => [
                'endpoints' => $endpoints,
                'total_count' => count($endpoints)
            ]
        ]);
    }
}

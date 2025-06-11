<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Grade;
use App\Models\Mention;
use App\Models\Label;
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

    /**
     * Get all domains for filter dropdowns.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/domains",
     *     summary="Get a list of all domains",
     *     tags={"Lists"},
     *     @OA\Response(
     *         response=200,
     *         description="List of domains",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Computer Science"),
     *                     @OA\Property(property="description", type="string", nullable=true, example="Study of computers and computational systems")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function domains(): JsonResponse
    {
        $domains = Domain::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $domains
        ]);
    }

    /**
     * Get all grades for filter dropdowns.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/grades",
     *     summary="Get a list of all grades",
     *     tags={"Lists"},
     *     @OA\Response(
     *         response=200,
     *         description="List of grades",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Licence"),
     *                     @OA\Property(property="level", type="integer", example=1),
     *                     @OA\Property(property="description", type="string", nullable=true, example="Bachelor's degree equivalent")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function grades(): JsonResponse
    {
        $grades = Grade::select('id', 'name', 'level', 'description')
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $grades
        ]);
    }

    /**
     * Get all mentions for filter dropdowns.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/mentions",
     *     summary="Get a list of all mentions",
     *     tags={"Lists"},
     *     @OA\Response(
     *         response=200,
     *         description="List of mentions",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Computer Science"),
     *                     @OA\Property(property="description", type="string", nullable=true, example="Study of algorithms and computational systems")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function mentions(): JsonResponse
    {
        $mentions = Mention::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $mentions
        ]);
    }

    /**
     * Get all labels for filter dropdowns.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/labels",
     *     summary="Get a list of all labels",
     *     tags={"Lists"},
     *     @OA\Response(
     *         response=200,
     *         description="List of labels",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Excellence"),
     *                     @OA\Property(property="description", type="string", nullable=true, example="Institution recognized for excellence")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function labels(): JsonResponse
    {
        $labels = Label::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $labels
        ]);
    }
}

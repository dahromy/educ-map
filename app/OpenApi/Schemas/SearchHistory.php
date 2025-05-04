<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="SearchHistory",
 *     title="Search History",
 *     description="User saved search history"
 * )
 */
class SearchHistory
{
    /**
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     example=1,
     *     description="Search history ID"
     * )
     */
    public $id;

    /**
     * @OA\Property(
     *     property="user_id",
     *     type="integer",
     *     example=1,
     *     description="User ID who saved this search"
     * )
     */
    public $user_id;

    /**
     * @OA\Property(
     *     property="search_query_text",
     *     type="string",
     *     example="computer science",
     *     description="Text of the search query"
     * )
     */
    public $search_query_text;

    /**
     * @OA\Property(
     *     property="search_filters",
     *     type="object",
     *     description="JSON object of search filters",
     *     example={
     *         "region": "Analamanga",
     *         "category": "Public University",
     *         "domain": "Computer Science"
     *     }
     * )
     */
    public $search_filters;

    /**
     * @OA\Property(
     *     property="created_at",
     *     type="string",
     *     format="date-time",
     *     example="2023-05-02T12:34:56Z",
     *     description="Creation timestamp"
     * )
     */
    public $created_at;

    /**
     * @OA\Property(
     *     property="updated_at",
     *     type="string",
     *     format="date-time",
     *     example="2023-05-02T12:34:56Z",
     *     description="Last update timestamp"
     * )
     */
    public $updated_at;
}

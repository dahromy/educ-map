<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     title="Pagination Links",
 *     description="Links for paginated responses"
 * )
 */
class PaginationLinksSchema
{
    /**
     * @OA\Property(
     *     property="first",
     *     type="string",
     *     format="url",
     *     example="http://localhost:8000/api/establishments?page=1",
     *     description="URL to the first page"
     * )
     */
    public $first;

    /**
     * @OA\Property(
     *     property="last",
     *     type="string",
     *     format="url",
     *     example="http://localhost:8000/api/establishments?page=10",
     *     description="URL to the last page"
     * )
     */
    public $last;

    /**
     * @OA\Property(
     *     property="prev",
     *     type="string",
     *     format="url",
     *     nullable=true,
     *     example=null,
     *     description="URL to the previous page (null if on first page)"
     * )
     */
    public $prev;

    /**
     * @OA\Property(
     *     property="next",
     *     type="string",
     *     format="url",
     *     example="http://localhost:8000/api/establishments?page=2",
     *     nullable=true,
     *     description="URL to the next page (null if on last page)"
     * )
     */
    public $next;
}

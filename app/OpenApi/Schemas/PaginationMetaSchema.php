<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     title="Pagination Metadata",
 *     description="Metadata for paginated responses"
 * )
 */
class PaginationMetaSchema
{
    /**
     * @OA\Property(
     *     property="current_page",
     *     type="integer",
     *     example=1,
     *     description="Current page number"
     * )
     */
    public $current_page;

    /**
     * @OA\Property(
     *     property="from",
     *     type="integer",
     *     example=1,
     *     description="First record index in current page"
     * )
     */
    public $from;

    /**
     * @OA\Property(
     *     property="last_page",
     *     type="integer",
     *     example=10,
     *     description="Last page number"
     * )
     */
    public $last_page;

    /**
     * @OA\Property(
     *     property="links",
     *     type="array",
     *     description="Page links",
     *     @OA\Items(
     *         type="object",
     *         @OA\Property(property="url", type="string", format="url", nullable=true, example="http://localhost:8000/api/establishments?page=2"),
     *         @OA\Property(property="label", type="string", example="2"),
     *         @OA\Property(property="active", type="boolean", example=false)
     *     )
     * )
     */
    public $links;

    /**
     * @OA\Property(
     *     property="path",
     *     type="string",
     *     format="url",
     *     example="http://localhost:8000/api/establishments",
     *     description="Base URL path"
     * )
     */
    public $path;

    /**
     * @OA\Property(
     *     property="per_page",
     *     type="integer",
     *     example=15,
     *     description="Number of items per page"
     * )
     */
    public $per_page;

    /**
     * @OA\Property(
     *     property="to",
     *     type="integer",
     *     example=15,
     *     description="Last record index in current page"
     * )
     */
    public $to;

    /**
     * @OA\Property(
     *     property="total",
     *     type="integer",
     *     example=150,
     *     description="Total number of items"
     * )
     */
    public $total;
}

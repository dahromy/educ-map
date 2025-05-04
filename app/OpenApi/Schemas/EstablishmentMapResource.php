<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="EstablishmentMapResource",
 *     title="Establishment Map Resource",
 *     description="Lightweight establishment resource for map markers"
 * )
 */
class EstablishmentMapResource
{
    /**
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     example=1,
     *     description="Establishment ID"
     * )
     */
    public $id;

    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     example="University of Antananarivo",
     *     description="Full name of the establishment"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="abbreviation",
     *     type="string",
     *     example="UA",
     *     description="Abbreviation/acronym of the establishment"
     * )
     */
    public $abbreviation;

    /**
     * @OA\Property(
     *     property="latitude",
     *     type="number",
     *     format="float",
     *     example=-18.9167,
     *     description="Geographical latitude"
     * )
     */
    public $latitude;

    /**
     * @OA\Property(
     *     property="longitude",
     *     type="number",
     *     format="float",
     *     example=47.5167,
     *     description="Geographical longitude"
     * )
     */
    public $longitude;

    /**
     * @OA\Property(
     *     property="category_name",
     *     type="string",
     *     example="Public University",
     *     description="Category name for display on map"
     * )
     */
    public $category_name;
}

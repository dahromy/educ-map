<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="EstablishmentResource",
 *     title="Establishment Resource",
 *     description="Establishment resource for list views"
 * )
 */
class EstablishmentResource
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
     *     property="category",
     *     type="object",
     *     description="Category information",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Public University")
     * )
     */
    public $category;

    /**
     * @OA\Property(
     *     property="location",
     *     type="object",
     *     description="Location information",
     *     @OA\Property(property="region", type="string", example="Analamanga"),
     *     @OA\Property(property="city", type="string", example="Antananarivo"),
     *     @OA\Property(property="latitude", type="number", format="float", example=-18.9167),
     *     @OA\Property(property="longitude", type="number", format="float", example=47.5167)
     * )
     */
    public $location;

    /**
     * @OA\Property(
     *     property="logo_url",
     *     type="string",
     *     format="url",
     *     example="http://example.com/ua-logo.png",
     *     description="URL to the establishment's logo"
     * )
     */
    public $logo_url;
}

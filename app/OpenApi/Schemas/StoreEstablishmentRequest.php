<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="StoreEstablishmentRequest",
 *     title="Store Establishment Request",
 *     description="Request for creating a new establishment",
 *     required={"name", "category_id"}
 * )
 */
class StoreEstablishmentRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     example="New University",
     *     description="Full name of the establishment"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="abbreviation",
     *     type="string",
     *     example="NU",
     *     description="Abbreviation/acronym of the establishment"
     * )
     */
    public $abbreviation;

    /**
     * @OA\Property(
     *     property="description",
     *     type="string",
     *     example="A new university in Madagascar",
     *     description="Detailed description of the establishment"
     * )
     */
    public $description;

    /**
     * @OA\Property(
     *     property="category_id",
     *     type="integer",
     *     example=1,
     *     description="Category ID"
     * )
     */
    public $category_id;

    /**
     * @OA\Property(
     *     property="address",
     *     type="string",
     *     example="123 University Street, Antananarivo",
     *     description="Physical address"
     * )
     */
    public $address;

    /**
     * @OA\Property(
     *     property="region",
     *     type="string",
     *     example="Analamanga",
     *     description="Region name"
     * )
     */
    public $region;

    /**
     * @OA\Property(
     *     property="city",
     *     type="string",
     *     example="Antananarivo",
     *     description="City name"
     * )
     */
    public $city;

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
     *     property="phone",
     *     type="string",
     *     example="+261 20 22 123 45",
     *     description="Contact phone number"
     * )
     */
    public $phone;

    /**
     * @OA\Property(
     *     property="email",
     *     type="string",
     *     format="email",
     *     example="contact@newuniversity.mg",
     *     description="Contact email address"
     * )
     */
    public $email;

    /**
     * @OA\Property(
     *     property="website",
     *     type="string",
     *     format="url",
     *     example="http://www.newuniversity.mg",
     *     description="Website URL"
     * )
     */
    public $website;

    /**
     * @OA\Property(
     *     property="logo_url",
     *     type="string",
     *     format="url",
     *     example="http://example.com/logo.png",
     *     description="URL to the establishment's logo"
     * )
     */
    public $logo_url;

    /**
     * @OA\Property(
     *     property="student_count",
     *     type="integer",
     *     example=5000,
     *     description="Number of students"
     * )
     */
    public $student_count;

    /**
     * @OA\Property(
     *     property="success_rate",
     *     type="number",
     *     format="float",
     *     example=80.5,
     *     description="Success rate percentage"
     * )
     */
    public $success_rate;

    /**
     * @OA\Property(
     *     property="professional_insertion_rate",
     *     type="number",
     *     format="float",
     *     example=75.0,
     *     description="Professional insertion rate percentage"
     * )
     */
    public $professional_insertion_rate;

    /**
     * @OA\Property(
     *     property="first_habilitation_year",
     *     type="integer",
     *     example=2020,
     *     description="Year of first habilitation"
     * )
     */
    public $first_habilitation_year;
}

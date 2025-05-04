<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="EstablishmentDetailResource",
 *     title="Establishment Detail Resource",
 *     description="Detailed establishment resource for single view"
 * )
 */
class EstablishmentDetailResource
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
     *     property="description",
     *     type="string",
     *     example="The largest and oldest university in Madagascar",
     *     description="Detailed description of the establishment"
     * )
     */
    public $description;

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
     *     @OA\Property(property="address", type="string", example="Ankatso, 101 Antananarivo"),
     *     @OA\Property(property="region", type="string", example="Analamanga"),
     *     @OA\Property(property="city", type="string", example="Antananarivo"),
     *     @OA\Property(property="latitude", type="number", format="float", example=-18.9167),
     *     @OA\Property(property="longitude", type="number", format="float", example=47.5167)
     * )
     */
    public $location;

    /**
     * @OA\Property(
     *     property="contact",
     *     type="object",
     *     description="Contact information",
     *     @OA\Property(property="phone", type="string", example="+261 20 22 326 39"),
     *     @OA\Property(property="email", type="string", example="contact@univ-antananarivo.mg"),
     *     @OA\Property(property="website", type="string", example="http://www.univ-antananarivo.mg")
     * )
     */
    public $contact;

    /**
     * @OA\Property(
     *     property="indicators",
     *     type="object",
     *     description="Performance indicators",
     *     @OA\Property(property="student_count", type="integer", example=30000),
     *     @OA\Property(property="success_rate", type="number", format="float", example=75.5),
     *     @OA\Property(property="professional_insertion_rate", type="number", format="float", example=68.0),
     *     @OA\Property(property="first_habilitation_year", type="integer", example=1961)
     * )
     */
    public $indicators;

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

    /**
     * @OA\Property(
     *     property="departments",
     *     type="array",
     *     description="List of departments in this establishment",
     *     @OA\Items(
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Computer Science Department"),
     *         @OA\Property(property="abbreviation", type="string", example="CS")
     *     )
     * )
     */
    public $departments;

    /**
     * @OA\Property(
     *     property="labels",
     *     type="array",
     *     description="List of labels for this establishment",
     *     @OA\Items(
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Excellence")
     *     )
     * )
     */
    public $labels;

    /**
     * @OA\Property(
     *     property="program_offerings",
     *     type="array",
     *     description="Programs offered by this establishment",
     *     @OA\Items(
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="domain", type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Computer Science")
     *         ),
     *         @OA\Property(property="grade", type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Licence")
     *         ),
     *         @OA\Property(property="mention", type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Information Systems")
     *         ),
     *         @OA\Property(property="tuition_fees_info", type="string", example="2,000,000 Ar per year"),
     *         @OA\Property(property="program_duration_info", type="string", example="3 years"),
     *         @OA\Property(property="accreditations", type="array", 
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="reference_type", type="string", example="Initial"),
     *                 @OA\Property(property="accreditation_date", type="string", format="date", example="2020-05-15"),
     *                 @OA\Property(property="is_recent", type="boolean", example=true),
     *                 @OA\Property(property="reference", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Decree No. 2020-123"),
     *                     @OA\Property(property="main_date", type="string", format="date", example="2020-05-15"),
     *                     @OA\Property(property="document_url", type="string", example="http://example.com/decree-2020-123.pdf")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public $program_offerings;

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

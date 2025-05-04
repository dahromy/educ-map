<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="UpdateEstablishmentRequest",
 *     title="Update Establishment Request",
 *     description="Request for updating an existing establishment"
 * )
 */
class UpdateEstablishmentRequest
{
    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     example="Updated University Name",
     *     description="Full name of the establishment"
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     property="abbreviation",
     *     type="string",
     *     example="UUN",
     *     description="Abbreviation/acronym of the establishment"
     * )
     */
    public $abbreviation;

    /**
     * @OA\Property(
     *     property="description",
     *     type="string",
     *     example="Updated description for the university",
     *     description="Detailed description of the establishment"
     * )
     */
    public $description;

    /**
     * @OA\Property(
     *     property="address",
     *     type="string",
     *     example="456 New University Road, Antananarivo",
     *     description="Physical address"
     * )
     */
    public $address;

    /**
     * @OA\Property(
     *     property="phone",
     *     type="string",
     *     example="+261 20 22 987 65",
     *     description="Contact phone number"
     * )
     */
    public $phone;

    /**
     * @OA\Property(
     *     property="email",
     *     type="string",
     *     format="email",
     *     example="updated-contact@university.mg",
     *     description="Contact email address"
     * )
     */
    public $email;

    /**
     * @OA\Property(
     *     property="website",
     *     type="string",
     *     format="url",
     *     example="http://www.updated-university.mg",
     *     description="Website URL"
     * )
     */
    public $website;

    /**
     * @OA\Property(
     *     property="logo_url",
     *     type="string",
     *     format="url",
     *     example="http://example.com/updated-logo.png",
     *     description="URL to the establishment's logo"
     * )
     */
    public $logo_url;

    /**
     * @OA\Property(
     *     property="student_count",
     *     type="integer",
     *     example=6000,
     *     description="Number of students"
     * )
     */
    public $student_count;

    /**
     * @OA\Property(
     *     property="success_rate",
     *     type="number",
     *     format="float",
     *     example=82.5,
     *     description="Success rate percentage"
     * )
     */
    public $success_rate;

    /**
     * @OA\Property(
     *     property="professional_insertion_rate",
     *     type="number",
     *     format="float",
     *     example=78.0,
     *     description="Professional insertion rate percentage"
     * )
     */
    public $professional_insertion_rate;
}

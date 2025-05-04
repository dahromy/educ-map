<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="EstablishmentComparisonResource",
 *     title="Establishment Comparison Resource",
 *     description="Resource for comparing establishments side by side"
 * )
 */
class EstablishmentComparisonResource
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
     *     property="indicators",
     *     type="object",
     *     description="Performance indicators",
     *     @OA\Property(property="student_count", type="integer", example=30000),
     *     @OA\Property(property="success_rate", type="number", format="float", example=75.5),
     *     @OA\Property(property="professional_insertion_rate", type="number", format="float", example=68.0)
     * )
     */
    public $indicators;

    /**
     * @OA\Property(
     *     property="program_info",
     *     type="object",
     *     description="Program information",
     *     @OA\Property(property="tuition_fees", type="string", example="Average: 2,000,000 Ar per year"),
     *     @OA\Property(property="duration", type="string", example="Range: 3-5 years")
     * )
     */
    public $program_info;
}

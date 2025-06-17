<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="EstablishmentResource",
 *     title="Establishment Resource",
 *     description="Establishment resource for list view with enhanced details",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Ecole Supérieure des Sciences Agronomiques"),
 *     @OA\Property(property="abbreviation", type="string", example="ESSA"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Leading agricultural sciences university in Madagascar"),
 *     @OA\Property(property="logo_url", type="string", nullable=true, example="https://educ-map.mg/storage/logos/essa.png"),
 *     @OA\Property(
 *         property="category",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Public University")
 *     ),
 *     @OA\Property(
 *         property="location",
 *         type="object",
 *         @OA\Property(property="address", type="string", example="BP 175, 101 Antananarivo, Madagascar"),
 *         @OA\Property(property="region", type="string", example="Analamanga"),
 *         @OA\Property(property="city", type="string", example="Antananarivo"),
 *         @OA\Property(property="latitude", type="number", format="float", example=-18.916779),
 *         @OA\Property(property="longitude", type="number", format="float", example=47.520526)
 *     ),
 *     @OA\Property(
 *         property="contact",
 *         type="object",
 *         @OA\Property(property="phone", type="string", nullable=true, example="+261 20 22 123 45"),
 *         @OA\Property(property="email", type="string", nullable=true, example="contact@essa.mg"),
 *         @OA\Property(property="website", type="string", nullable=true, example="https://www.essa.mg")
 *     ),
 *     @OA\Property(
 *         property="indicators",
 *         type="object",
 *         @OA\Property(property="student_count", type="integer", nullable=true, example=2500),
 *         @OA\Property(property="success_rate", type="number", format="float", nullable=true, example=85.5),
 *         @OA\Property(property="professional_insertion_rate", type="number", format="float", nullable=true, example=78.2),
 *         @OA\Property(property="first_habilitation_year", type="integer", nullable=true, example=1995)
 *     ),
 *     @OA\Property(
 *         property="labels",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Excellence"),
 *             @OA\Property(property="description", type="string", example="Institution of excellence recognized for outstanding performance")
 *         )
 *     ),
 *     @OA\Property(
 *         property="programs_summary",
 *         type="object",
 *         @OA\Property(property="total_programs", type="integer", example=15),
 *         @OA\Property(property="domains_count", type="integer", example=5),
 *         @OA\Property(property="grades_offered", type="array", @OA\Items(type="string"), example={"Licence", "Master", "Doctorat"}),
 *         @OA\Property(property="departments_count", type="integer", example=8)
 *     ),
 *     @OA\Property(
 *         property="recent_accreditation",
 *         type="object",
 *         @OA\Property(property="has_recent", type="boolean", example=true),
 *         @OA\Property(property="accreditation_date", type="string", format="date", nullable=true, example="2024-09-15")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-01T14:45:00.000000Z")
 * )
 */
class EstablishmentResourceSchema
{
    // This is just a placeholder class for OpenAPI annotations
}

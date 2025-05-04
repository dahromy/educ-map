<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="EstablishmentDetailResource",
 *     title="Establishment Detail Resource",
 *     description="Detailed establishment resource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Ecole Supérieure des Sciences Agronomiques"),
 *     @OA\Property(property="abbreviation", type="string", example="ESSA"),
 *     @OA\Property(property="logo_url", type="string", nullable=true, example="https://educ-map.mg/storage/logos/essa.png"),
 *     @OA\Property(property="address", type="string", example="BP 175, 101 Antananarivo, Madagascar"),
 *     @OA\Property(property="region", type="string", example="Analamanga"),
 *     @OA\Property(property="latitude", type="number", format="float", example=-18.916779),
 *     @OA\Property(property="longitude", type="number", format="float", example=47.520526),
 *     @OA\Property(property="website", type="string", nullable=true, example="https://essa.mg"),
 *     @OA\Property(property="email", type="string", nullable=true, example="contact@essa.mg"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+261 20 22 318 35"),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="student_count", type="integer", nullable=true, example=1200),
 *     @OA\Property(property="success_rate", type="number", format="float", nullable=true, example=85.5),
 *     @OA\Property(property="professional_insertion_rate", type="number", format="float", nullable=true, example=78.3),
 *     @OA\Property(property="first_habilitation_year", type="integer", nullable=true, example=1990),
 *     @OA\Property(
 *         property="category",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Public University")
 *     ),
 *     @OA\Property(
 *         property="departments",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Department of Agronomy")
 *         )
 *     ),
 *     @OA\Property(
 *         property="labels",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Excellence")
 *         )
 *     ),
 *     @OA\Property(
 *         property="program_offerings",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="department_id", type="integer", example=1),
 *             @OA\Property(property="department_name", type="string", example="Department of Agronomy"),
 *             @OA\Property(property="domain_id", type="integer", example=1),
 *             @OA\Property(property="domain_name", type="string", example="Agricultural Sciences"),
 *             @OA\Property(property="grade_id", type="integer", example=1),
 *             @OA\Property(property="grade_name", type="string", example="Master"),
 *             @OA\Property(property="mention_id", type="integer", example=1),
 *             @OA\Property(property="mention_name", type="string", example="Agricultural Engineering"),
 *             @OA\Property(property="tuition_fees_info", type="string", nullable=true, example="1,500,000 Ar/year"),
 *             @OA\Property(property="program_duration_info", type="string", example="2 years"),
 *             @OA\Property(
 *                 property="accreditations",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="reference_id", type="integer", example=1),
 *                     @OA\Property(property="reference_type", type="string", example="ministerial_decree"),
 *                     @OA\Property(property="accreditation_date", type="string", format="date", example="2022-07-15"),
 *                     @OA\Property(property="is_recent", type="boolean", example=true),
 *                     @OA\Property(
 *                         property="reference",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="main_date", type="string", format="date", example="2022-07-15"),
 *                         @OA\Property(property="document_url", type="string", nullable=true, example="https://educ-map.mg/storage/decrees/decree_2022_125.pdf")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class EstablishmentDetailResourceSchema
{
    // This is just a placeholder class for OpenAPI annotations
}

<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="EstablishmentResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="The ID of the establishment."),
 *     @OA\Property(property="name", type="string", description="The name of the establishment."),
 *     @OA\Property(property="abbreviation", type="string", description="The abbreviation of the establishment."),
 *     @OA\Property(property="description", type="string", description="The description of the establishment."),
 *     @OA\Property(property="category", type="object",
 *         @OA\Property(property="id", type="integer", description="The ID of the category."),
 *         @OA\Property(property="name", type="string", description="The name of the category.")
 *     ),
 *     @OA\Property(property="location", type="object",
 *         @OA\Property(property="address", type="string", description="The address of the establishment."),
 *         @OA\Property(property="region", type="string", description="The region of the establishment."),
 *         @OA\Property(property="city", type="string", description="The city of the establishment."),
 *         @OA\Property(property="latitude", type="number", description="The latitude of the establishment."),
 *         @OA\Property(property="longitude", type="number", description="The longitude of the establishment.")
 *     ),
 *     @OA\Property(property="contact", type="object",
 *         @OA\Property(property="phone", type="string", description="The phone number of the establishment."),
 *         @OA\Property(property="email", type="string", description="The email of the establishment."),
 *         @OA\Property(property="website", type="string", description="The website of the establishment.")
 *     ),
 *     @OA\Property(property="indicators", type="object",
 *         @OA\Property(property="student_count", type="integer", description="The number of students in the establishment."),
 *         @OA\Property(property="success_rate", type="number", description="The success rate of the establishment."),
 *         @OA\Property(property="professional_insertion_rate", type="number", description="The professional insertion rate of the establishment."),
 *         @OA\Property(property="first_habilitation_year", type="integer", description="The first habilitation year of the establishment."),
 *         @OA\Property(property="status", type="string", description="The status of the establishment."),
 *         @OA\Property(property="international_partnerships", type="string", description="The international partnerships of the establishment.")
 *     ),
 *     @OA\Property(property="labels", type="array",
 *         @OA\Items(type="object",
 *             @OA\Property(property="id", type="integer", description="The ID of the label."),
 *             @OA\Property(property="name", type="string", description="The name of the label."),
 *             @OA\Property(property="description", type="string", description="The description of the label.")
 *         )
 *     ),
 *     @OA\Property(property="programs_summary", type="object",
 *         @OA\Property(property="total_programs", type="integer", description="The total number of programs in the establishment."),
 *         @OA\Property(property="domains_count", type="integer", description="The number of domains in the establishment."),
 *         @OA\Property(property="grades_offered", type="array", @OA\Items(type="string"), description="The grades offered in the establishment."),
 *         @OA\Property(property="departments_count", type="integer", description="The number of departments in the establishment.")
 *     ),
 *     @OA\Property(property="recent_accreditation", type="object",
 *         @OA\Property(property="has_recent", type="boolean", description="Whether the establishment has recent accreditation."),
 *         @OA\Property(property="accreditation_date", type="string", description="The date of the recent accreditation.")
 *     ),
 *     @OA\Property(property="logo_url", type="string", description="The URL of the logo of the establishment."),
 *     @OA\Property(property="created_at", type="string", description="The creation date of the establishment."),
 *     @OA\Property(property="updated_at", type="string", description="The update date of the establishment.")
 * )
 */
class EstablishmentResourceSchema
{
    // This is just a placeholder class for OpenAPI annotations
}

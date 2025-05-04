<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="EstablishmentResource",
 *     title="Establishment Resource",
 *     description="Establishment resource for list view",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Ecole Supérieure des Sciences Agronomiques"),
 *     @OA\Property(property="abbreviation", type="string", example="ESSA"),
 *     @OA\Property(property="logo_url", type="string", nullable=true, example="https://educ-map.mg/storage/logos/essa.png"),
 *     @OA\Property(property="address", type="string", example="BP 175, 101 Antananarivo, Madagascar"),
 *     @OA\Property(property="region", type="string", example="Analamanga"),
 *     @OA\Property(property="latitude", type="number", format="float", example=-18.916779),
 *     @OA\Property(property="longitude", type="number", format="float", example=47.520526),
 *     @OA\Property(
 *         property="category",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Public University")
 *     )
 * )
 */
class EstablishmentResourceSchema
{
    // This is just a placeholder class for OpenAPI annotations
}

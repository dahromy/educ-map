<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="MapMarker",
 *     title="Map Marker",
 *     description="Lightweight establishment data for map markers",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Ecole Supérieure des Sciences Agronomiques"),
 *     @OA\Property(property="abbreviation", type="string", example="ESSA"),
 *     @OA\Property(property="latitude", type="number", format="float", example=-18.916779),
 *     @OA\Property(property="longitude", type="number", format="float", example=47.520526),
 *     @OA\Property(property="category_name", type="string", example="Public University"),
 * )
 */
class MapMarkerSchema
{
    // This is just a placeholder class for OpenAPI annotations
}

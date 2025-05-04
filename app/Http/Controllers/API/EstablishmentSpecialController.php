<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CompareEstablishmentsRequest;
use App\Http\Resources\API\EstablishmentComparisonResource;
use App\Http\Resources\API\EstablishmentMapResource;
use App\Http\Resources\API\EstablishmentResource;
use App\Models\Establishment;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Map & Comparison",
 *     description="Endpoints for map markers and establishment comparison"
 * )
 */
class EstablishmentSpecialController extends Controller
{
    /**
     * Get recently added establishments or those with recent accreditations.
     *
     * @return AnonymousResourceCollection
     *
     * @OA\Get(
     *     path="/api/establishments/recent",
     *     summary="Get recently added establishments",
     *     tags={"Establishments"},
     *     @OA\Response(
     *         response=200,
     *         description="List of recent establishments",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="abbreviation", type="string"),
     *                     @OA\Property(property="logo_url", type="string", nullable=true),
     *                     @OA\Property(property="address", type="string"),
     *                     @OA\Property(property="region", type="string"),
     *                     @OA\Property(property="category", type="object")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function recent(): AnonymousResourceCollection
    {
        $establishments = Establishment::with('category')
            ->recent()
            ->limit(10)
            ->get();

        return EstablishmentResource::collection($establishments);
    }

    /**
     * Get lightweight establishment data for map markers.
     *
     * @return AnonymousResourceCollection
     *
     * @OA\Get(
     *     path="/api/map/markers",
     *     summary="Get lightweight establishment data for map markers",
     *     tags={"Map & Comparison"},
     *     @OA\Response(
     *         response=200,
     *         description="List of map marker data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Ecole Supérieure des Sciences Agronomiques"),
     *                     @OA\Property(property="abbreviation", type="string", example="ESSA"),
     *                     @OA\Property(property="latitude", type="number", format="float", example=-18.916779),
     *                     @OA\Property(property="longitude", type="number", format="float", example=47.520526),
     *                     @OA\Property(property="category_name", type="string", example="Public University")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function mapMarkers(): AnonymousResourceCollection
    {
        $establishments = Establishment::with('category')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return EstablishmentMapResource::collection($establishments);
    }

    /**
     * Compare multiple establishments side by side.
     *
     * @param CompareEstablishmentsRequest $request
     * @return AnonymousResourceCollection
     *
     * @OA\Get(
     *     path="/api/compare",
     *     summary="Compare multiple establishments side by side",
     *     tags={"Map & Comparison"},
     *     @OA\Parameter(
     *         name="ids[]",
     *         in="query",
     *         description="Array of establishment IDs to compare",
     *         required=true,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="integer")
     *         ),
     *         style="form",
     *         explode=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comparison data for selected establishments",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="abbreviation", type="string"),
     *                     @OA\Property(property="student_count", type="integer"),
     *                     @OA\Property(property="success_rate", type="number", format="float"),
     *                     @OA\Property(property="professional_insertion_rate", type="number", format="float"),
     *                     @OA\Property(property="tuition_fees_info", type="string"),
     *                     @OA\Property(property="program_duration_info", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function compare(CompareEstablishmentsRequest $request): AnonymousResourceCollection
    {
        $establishments = Establishment::with('programOfferings')
            ->whereIn('id', $request->ids)
            ->get();

        return EstablishmentComparisonResource::collection($establishments);
    }
}

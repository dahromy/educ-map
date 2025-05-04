<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CompareEstablishmentsRequest;
use App\Http\Resources\API\EstablishmentComparisonResource;
use App\Http\Resources\API\EstablishmentMapResource;
use App\Http\Resources\API\EstablishmentResource;
use App\Models\Establishment;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EstablishmentSpecialController extends Controller
{
    /**
     * Get recently added establishments or those with recent accreditations.
     *
     * @return AnonymousResourceCollection
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
     */
    public function compare(CompareEstablishmentsRequest $request): AnonymousResourceCollection
    {
        $establishments = Establishment::with('programOfferings')
            ->whereIn('id', $request->ids)
            ->get();

        return EstablishmentComparisonResource::collection($establishments);
    }
}

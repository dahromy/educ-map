<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\IndexEstablishmentRequest;
use App\Http\Requests\API\StoreEstablishmentRequest;
use App\Http\Requests\API\UpdateEstablishmentRequest;
use App\Http\Resources\API\EstablishmentDetailResource;
use App\Http\Resources\API\EstablishmentResource;
use App\Models\Establishment;
use App\Policies\EstablishmentPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class EstablishmentController extends Controller
{
    /**
     * Display a listing of the establishments with filtering options.
     *
     * @param IndexEstablishmentRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexEstablishmentRequest $request): AnonymousResourceCollection
    {
        $query = Establishment::with('category')
            ->filterByRegion($request->region)
            ->filterByName($request->name)
            ->filterByAbbreviation($request->abbreviation)
            ->filterByCategory($request->category);

        // Apply advanced filters if provided
        if ($request->has('domain')) {
            $query->filterByDomain($request->domain);
        }

        if ($request->has('label')) {
            $query->filterByLabel($request->label);
        }

        if ($request->has('reference_start_date') || $request->has('reference_end_date')) {
            $query->filterByReferenceDate(
                $request->reference_start_date,
                $request->reference_end_date
            );
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', 'name');
        $sortDirection = $request->input('sort_direction', 'asc');

        if ($sortBy === 'name' || $sortBy === 'student_count') {
            $query->orderBy($sortBy, $sortDirection);
        } elseif ($sortBy === 'reference_date') {
            // Sort by most recent reference date requires a more complex query
            $query->orderBy(function ($query) use ($sortDirection) {
                $query->select('references.main_date')
                    ->from('references')
                    ->join('accreditations', 'references.id', '=', 'accreditations.reference_id')
                    ->join('program_offerings', 'accreditations.program_offering_id', '=', 'program_offerings.id')
                    ->whereColumn('program_offerings.establishment_id', 'establishments.id')
                    ->orderBy('references.main_date', $sortDirection)
                    ->limit(1);
            }, $sortDirection);
        }

        // Apply pagination
        $perPage = $request->input('per_page', 15);
        return EstablishmentResource::collection($query->paginate($perPage));
    }

    /**
     * Store a newly created establishment in storage.
     *
     * @param StoreEstablishmentRequest $request
     * @return EstablishmentDetailResource
     */
    public function store(StoreEstablishmentRequest $request): EstablishmentDetailResource
    {
        $establishment = Establishment::create($request->validated());

        // Attach labels if provided
        if ($request->has('labels')) {
            $establishment->labels()->attach($request->labels);
        }

        return new EstablishmentDetailResource(
            $establishment->load(['category', 'departments', 'labels'])
        );
    }

    /**
     * Display the specified establishment.
     *
     * @param Establishment $establishment
     * @return EstablishmentDetailResource
     */
    public function show(Establishment $establishment): EstablishmentDetailResource
    {
        return new EstablishmentDetailResource(
            $establishment->load([
                'category',
                'departments',
                'labels',
                'programOfferings',
                'programOfferings.domain',
                'programOfferings.grade',
                'programOfferings.mention',
                'programOfferings.accreditations',
                'programOfferings.accreditations.reference'
            ])
        );
    }

    /**
     * Update the specified establishment in storage.
     *
     * @param UpdateEstablishmentRequest $request
     * @param Establishment $establishment
     * @return EstablishmentDetailResource
     */
    public function update(UpdateEstablishmentRequest $request, Establishment $establishment): EstablishmentDetailResource
    {
        $establishment->update($request->validated());

        // Sync labels if provided
        if ($request->has('labels')) {
            $establishment->labels()->sync($request->labels);
        }

        return new EstablishmentDetailResource(
            $establishment->load(['category', 'departments', 'labels'])
        );
    }

    /**
     * Remove the specified establishment from storage.
     *
     * @param Establishment $establishment
     * @return JsonResponse
     */
    public function destroy(Establishment $establishment): JsonResponse
    {
        // Use Gate facade instead of authorize method
        if (!Gate::allows('delete', $establishment)) {
            return response()->json([
                'message' => 'Unauthorized. You do not have permission to delete this establishment.',
                'debug_info' => [
                    'user' => auth()->user() ? [
                        'id' => auth()->user()->id,
                        'roles' => auth()->user()->roles,
                        'associated_establishment' => auth()->user()->associated_establishment
                    ] : null,
                    'policies_loaded' => class_exists(EstablishmentPolicy::class),
                    'gate_check' => 'delete',
                    'establishment_id' => $establishment->id
                ]
            ], 403);
        }

        $establishment->delete();

        return response()->json([
            'message' => 'Establishment successfully deleted'
        ]);
    }
}

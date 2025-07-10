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

/**
 * @OA\Tag(
 *     name="Establishments",
 *     description="Endpoints for accessing and managing establishment data"
 * )
 */
class EstablishmentController extends Controller
{
    /**
     * Display a listing of the establishments with filtering options.
     *
     * @param IndexEstablishmentRequest $request
     * @return AnonymousResourceCollection
     *
     * @OA\Get(
     *     path="/api/establishments",
     *     summary="Get a paginated list of establishments with optional filtering and sorting",
     *     tags={"Establishments"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="General search query for filtering establishments",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="region",
     *         in="query",
     *         description="Filter by region name (exact match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter by establishment name (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="abbreviation",
     *         in="query",
     *         description="Filter by abbreviation (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category name (exact match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="domain",
     *         in="query",
     *         description="Filter by domain name (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="label",
     *         in="query",
     *         description="Filter by label name (exact match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="reference_start_date",
     *         in="query",
     *         description="Filter by references with date after this value (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="reference_end_date",
     *         in="query",
     *         description="Filter by references with date before this value (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Filter by city name (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="has_recent_accreditation",
     *         in="query",
     *         description="Filter by establishments with recent accreditations",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="min_student_count",
     *         in="query",
     *         description="Filter by minimum student count",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=0)
     *     ),
     *     @OA\Parameter(
     *         name="max_student_count",
     *         in="query",
     *         description="Filter by maximum student count",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=0)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"name", "student_count", "reference_date", "success_rate", "professional_insertion_rate", "first_habilitation_year"},
     *             default="name"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction (asc or desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1, minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15, minimum=1, maximum=100)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of establishments with enhanced details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Ecole Supérieure des Sciences Agronomiques"),
     *                     @OA\Property(property="abbreviation", type="string", example="ESSA"),
     *                     @OA\Property(property="description", type="string", nullable=true, example="Leading agricultural sciences university in Madagascar"),
     *                     @OA\Property(property="logo_url", type="string", nullable=true),
     *                     @OA\Property(property="category", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     ),
     *                     @OA\Property(property="location", type="object",
     *                         @OA\Property(property="address", type="string"),
     *                         @OA\Property(property="region", type="string"),
     *                         @OA\Property(property="city", type="string"),
     *                         @OA\Property(property="latitude", type="number", format="float"),
     *                         @OA\Property(property="longitude", type="number", format="float")
     *                     ),
     *                     @OA\Property(property="contact", type="object",
     *                         @OA\Property(property="phone", type="string", nullable=true),
     *                         @OA\Property(property="email", type="string", nullable=true),
     *                         @OA\Property(property="website", type="string", nullable=true)
     *                     ),
     *                     @OA\Property(property="indicators", type="object",
     *                         @OA\Property(property="student_count", type="integer", nullable=true),
     *                         @OA\Property(property="success_rate", type="number", format="float", nullable=true),
     *                         @OA\Property(property="professional_insertion_rate", type="number", format="float", nullable=true),
     *                         @OA\Property(property="first_habilitation_year", type="integer", nullable=true)
     *                     ),
     *                     @OA\Property(property="labels", type="array",
     *                         @OA\Items(type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string"),
     *                             @OA\Property(property="description", type="string")
     *                         )
     *                     ),
     *                     @OA\Property(property="programs_summary", type="object",
     *                         @OA\Property(property="total_programs", type="integer"),
     *                         @OA\Property(property="domains_count", type="integer"),
     *                         @OA\Property(property="grades_offered", type="array", @OA\Items(type="string")),
     *                         @OA\Property(property="departments_count", type="integer")
     *                     ),
     *                     @OA\Property(property="recent_accreditation", type="object",
     *                         @OA\Property(property="has_recent", type="boolean"),
     *                         @OA\Property(property="accreditation_date", type="string", format="date", nullable=true)
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="first", type="string"),
     *                 @OA\Property(property="last", type="string"),
     *                 @OA\Property(property="prev", type="string", nullable=true),
     *                 @OA\Property(property="next", type="string", nullable=true)
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="from", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="path", type="string"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="to", type="integer"),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="links", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="url", type="string", nullable=true),
     *                         @OA\Property(property="label", type="string"),
     *                         @OA\Property(property="active", type="boolean")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(IndexEstablishmentRequest $request): AnonymousResourceCollection
    {
        $query = Establishment::with([
            'category',
            'labels',
            'departments',
            'programOfferings' => function ($query) {
                $query->with(['grade', 'accreditations']);
            }
        ])
            ->filterByRegion($request->region)
            ->filterByName($request->name)
            ->filterByAbbreviation($request->abbreviation)
            ->filterByCategory($request->category)
            ->filterByCity($request->city)
            ->filterByStudentCount($request->min_student_count, $request->max_student_count);

        // Unified search: q or query (case-insensitive, all fields/relations)
        $search = strtolower($request->input('q', $request->input('query', '')));
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(abbreviation) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(address) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(region) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(city) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(website) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(logo_url) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('CAST(student_count AS TEXT) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('CAST(success_rate AS TEXT) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('CAST(professional_insertion_rate AS TEXT) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('CAST(first_habilitation_year AS TEXT) LIKE ?', ["%{$search}%"])
                    // Category name
                    ->orWhereHas('category', function ($cat) use ($search) {
                        $cat->whereRaw('LOWER(category_name) LIKE ?', ["%{$search}%"]);
                    })
                    // Departments name
                    ->orWhereHas('departments', function ($dept) use ($search) {
                        $dept->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    })
                    // Labels name
                    ->orWhereHas('labels', function ($label) use ($search) {
                        $label->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    })
                    // Program Offerings: domain, grade, mention
                    ->orWhereHas('programOfferings.domain', function ($domain) use ($search) {
                        $domain->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    })
                    ->orWhereHas('programOfferings.grade', function ($grade) use ($search) {
                        $grade->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    })
                    ->orWhereHas('programOfferings.mention', function ($mention) use ($search) {
                        $mention->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

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

        if ($request->has('has_recent_accreditation')) {
            $hasRecentAccreditation = filter_var($request->has_recent_accreditation, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($hasRecentAccreditation !== null) {
                $query->filterByRecentAccreditation($hasRecentAccreditation);
            }
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', 'name');
        $sortDirection = $request->input('sort_direction', 'asc');

        if (in_array($sortBy, ['name', 'student_count', 'success_rate', 'professional_insertion_rate', 'first_habilitation_year'])) {
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
     *
     * @OA\Post(
     *     path="/api/establishments",
     *     summary="Create a new establishment",
     *     tags={"Establishments"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Establishment data",
     *         @OA\JsonContent(
     *             required={"name", "category_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="abbreviation", type="string"),
     *             @OA\Property(property="category_id", type="integer"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="region", type="string"),
     *             @OA\Property(property="latitude", type="number", format="float"),
     *             @OA\Property(property="longitude", type="number", format="float"),
     *             @OA\Property(property="website", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="student_count", type="integer"),
     *             @OA\Property(property="success_rate", type="number", format="float"),
     *             @OA\Property(property="professional_insertion_rate", type="number", format="float"),
     *             @OA\Property(property="first_habilitation_year", type="integer"),
     *             @OA\Property(property="labels", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Establishment created successfully",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
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
     *
     * @OA\Get(
     *     path="/api/establishments/{id}",
     *     summary="Get establishment details",
     *     tags={"Establishments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the establishment to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Establishment details",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="abbreviation", type="string"),
     *                 @OA\Property(property="logo_url", type="string", nullable=true),
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="region", type="string"),
     *                 @OA\Property(property="latitude", type="number", format="float"),
     *                 @OA\Property(property="longitude", type="number", format="float"),
     *                 @OA\Property(property="website", type="string", nullable=true),
     *                 @OA\Property(property="email", type="string", nullable=true),
     *                 @OA\Property(property="phone", type="string", nullable=true),
     *                 @OA\Property(property="description", type="string", nullable=true),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="student_count", type="integer", nullable=true),
     *                 @OA\Property(property="success_rate", type="number", format="float", nullable=true),
     *                 @OA\Property(property="professional_insertion_rate", type="number", format="float", nullable=true),
     *                 @OA\Property(property="first_habilitation_year", type="integer", nullable=true),
     *                 @OA\Property(property="category", type="object"),
     *                 @OA\Property(property="departments", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="labels", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="program_offerings", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Establishment not found")
     * )
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
     *
     * @OA\Put(
     *     path="/api/establishments/{id}",
     *     summary="Update an existing establishment",
     *     tags={"Establishments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the establishment to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="abbreviation", type="string"),
     *             @OA\Property(property="category_id", type="integer"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="region", type="string"),
     *             @OA\Property(property="latitude", type="number", format="float"),
     *             @OA\Property(property="longitude", type="number", format="float"),
     *             @OA\Property(property="website", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="student_count", type="integer"),
     *             @OA\Property(property="success_rate", type="number", format="float"),
     *             @OA\Property(property="professional_insertion_rate", type="number", format="float"),
     *             @OA\Property(property="first_habilitation_year", type="integer"),
     *             @OA\Property(property="labels", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Establishment updated successfully",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Establishment not found")
     * )
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
     *
     * @OA\Delete(
     *     path="/api/establishments/{id}",
     *     summary="Delete an establishment",
     *     tags={"Establishments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the establishment to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Establishment deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Establishment not found")
     * )
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

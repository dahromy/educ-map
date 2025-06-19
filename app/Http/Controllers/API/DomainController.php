<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreDomainRequest;
use App\Http\Requests\API\UpdateDomainRequest;
use App\Http\Resources\API\DomainResource;
use App\Models\Domain;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Domains",
 *     description="Endpoints for accessing and managing educational domains/fields of study"
 * )
 */
class DomainController extends Controller
{
    /**
     * Display a listing of the domains.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     *
     * @OA\Get(
     *     path="/api/domains",
     *     summary="Get a list of all domains",
     *     tags={"Domains"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by domain name (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by (name, created_at)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"name", "created_at"}, default="name")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction (asc or desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of domains",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/DomainResource"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Domain::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        $allowedSortFields = ['name', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $domains = $query->get();

        return DomainResource::collection($domains);
    }

    /**
     * Store a newly created domain in storage.
     *
     * @param StoreDomainRequest $request
     * @return DomainResource
     *
     * @OA\Post(
     *     path="/api/domains",
     *     summary="Create a new domain",
     *     tags={"Domains"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="description", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Domain created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/DomainResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreDomainRequest $request): DomainResource
    {
        $domain = Domain::create($request->validated());

        return new DomainResource($domain);
    }

    /**
     * Display the specified domain.
     *
     * @param Domain $domain
     * @return DomainResource
     *
     * @OA\Get(
     *     path="/api/domains/{id}",
     *     summary="Get a specific domain",
     *     tags={"Domains"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the domain to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Domain details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/DomainResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Domain not found")
     * )
     */
    public function show(Domain $domain): DomainResource
    {
        return new DomainResource($domain);
    }

    /**
     * Update the specified domain in storage.
     *
     * @param UpdateDomainRequest $request
     * @param Domain $domain
     * @return DomainResource
     *
     * @OA\Put(
     *     path="/api/domains/{id}",
     *     summary="Update a domain",
     *     tags={"Domains"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the domain to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="description", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Domain updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/DomainResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Domain not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateDomainRequest $request, Domain $domain): DomainResource
    {
        $domain->update($request->validated());

        return new DomainResource($domain);
    }

    /**
     * Remove the specified domain from storage.
     *
     * @param Domain $domain
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/api/domains/{id}",
     *     summary="Delete a domain",
     *     tags={"Domains"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the domain to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Domain deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Domain deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Domain not found"),
     *     @OA\Response(response=409, description="Conflict - Domain has associated program offerings")
     * )
     */
    public function destroy(Domain $domain): JsonResponse
    {
        // Check if domain has associated program offerings
        if ($domain->programOfferings()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete domain. It has associated program offerings.'
            ], 409);
        }

        $domain->delete();

        return response()->json([
            'message' => 'Domain deleted successfully'
        ]);
    }
}

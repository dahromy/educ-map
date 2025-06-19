<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\OfficialDocumentResource;
use App\Models\OfficialDocument;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Official Documents",
 *     description="Endpoints for accessing official documents"
 * )
 */
class OfficialDocumentController extends Controller
{
    /**
     * Display a listing of official documents.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     *
     * @OA\Get(
     *     path="/api/documents",
     *     summary="Get a list of official documents",
     *     tags={"Official Documents"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by document type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="active_only",
     *         in="query",
     *         description="Show only active documents (default: true)",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OfficialDocument")
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = OfficialDocument::query();

        // Filter by document type if provided
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Show only active documents by default
        $activeOnly = $request->boolean('active_only', true);
        if ($activeOnly) {
            $query->active();
        }

        // Eager load the reference relationship
        $query->with('reference');

        // Order by sort order and creation date
        $query->ordered();

        $documents = $query->get();

        return OfficialDocumentResource::collection($documents);
    }

    /**
     * Display the specified official document.
     *
     * @param OfficialDocument $document
     * @return OfficialDocumentResource
     *
     * @OA\Get(
     *     path="/api/documents/{id}",
     *     summary="Get a specific official document",
     *     tags={"Official Documents"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Document ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/OfficialDocument")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Document not found"
     *     )
     * )
     */
    public function show(OfficialDocument $document): OfficialDocumentResource
    {
        $document->load('reference');

        return new OfficialDocumentResource($document);
    }
}

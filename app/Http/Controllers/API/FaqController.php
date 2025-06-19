<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreFaqItemRequest;
use App\Http\Requests\API\UpdateFaqItemRequest;
use App\Http\Resources\API\FaqItemResource;
use App\Models\FaqItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="FAQ",
 *     description="Endpoints for managing FAQ items"
 * )
 */
class FaqController extends Controller
{
    /**
     * Display a listing of FAQ items.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     *
     * @OA\Get(
     *     path="/api/faq",
     *     summary="Get a list of FAQ items",
     *     tags={"FAQ"},
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="active_only",
     *         in="query",
     *         description="Show only active FAQ items (default: true)",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/FaqItem")
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = FaqItem::query();

        // Filter by category if provided
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Show only active items by default
        $activeOnly = $request->boolean('active_only', true);
        if ($activeOnly) {
            $query->active();
        }

        // Order by sort order and creation date
        $query->ordered();

        $faqItems = $query->get();

        return FaqItemResource::collection($faqItems);
    }

    /**
     * Store a newly created FAQ item in storage.
     *
     * @param StoreFaqItemRequest $request
     * @return FaqItemResource
     *
     * @OA\Post(
     *     path="/api/admin/faq",
     *     summary="Create a new FAQ item",
     *     tags={"FAQ"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question", "answer"},
     *             @OA\Property(property="question", type="string", maxLength=500),
     *             @OA\Property(property="answer", type="string"),
     *             @OA\Property(property="category", type="string", maxLength=100),
     *             @OA\Property(property="sort_order", type="integer", minimum=0),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="FAQ item created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/FaqItem")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    public function store(StoreFaqItemRequest $request): FaqItemResource
    {
        $faqItem = FaqItem::create($request->validated());

        return new FaqItemResource($faqItem);
    }

    /**
     * Display the specified FAQ item.
     *
     * @param FaqItem $faqItem
     * @return FaqItemResource
     *
     * @OA\Get(
     *     path="/api/faq/{id}",
     *     summary="Get a specific FAQ item",
     *     tags={"FAQ"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="FAQ item ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/FaqItem")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="FAQ item not found"
     *     )
     * )
     */
    public function show(FaqItem $faqItem): FaqItemResource
    {
        return new FaqItemResource($faqItem);
    }

    /**
     * Update the specified FAQ item in storage.
     *
     * @param UpdateFaqItemRequest $request
     * @param FaqItem $faqItem
     * @return FaqItemResource
     *
     * @OA\Put(
     *     path="/api/admin/faq/{id}",
     *     summary="Update a FAQ item",
     *     tags={"FAQ"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="FAQ item ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="question", type="string", maxLength=500),
     *             @OA\Property(property="answer", type="string"),
     *             @OA\Property(property="category", type="string", maxLength=100),
     *             @OA\Property(property="sort_order", type="integer", minimum=0),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="FAQ item updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/FaqItem")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="FAQ item not found"
     *     )
     * )
     */
    public function update(UpdateFaqItemRequest $request, FaqItem $faqItem): FaqItemResource
    {
        $faqItem->update($request->validated());

        return new FaqItemResource($faqItem);
    }

    /**
     * Remove the specified FAQ item from storage.
     *
     * @param FaqItem $faqItem
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/api/admin/faq/{id}",
     *     summary="Delete a FAQ item",
     *     tags={"FAQ"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="FAQ item ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="FAQ item deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="FAQ item not found"
     *     )
     * )
     */
    public function destroy(FaqItem $faqItem): JsonResponse
    {
        // Authorization is handled by the route middleware (admin only)
        $faqItem->delete();

        return response()->json(null, 204);
    }
}

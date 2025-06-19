<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="FaqItem",
 *     type="object",
 *     title="FAQ Item",
 *     description="Frequently Asked Question item",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="FAQ item ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="question",
 *         type="string",
 *         description="The question text",
 *         example="Comment puis-je m'inscrire dans un établissement?"
 *     ),
 *     @OA\Property(
 *         property="answer",
 *         type="string",
 *         description="The answer text",
 *         example="Pour vous inscrire, vous devez d'abord vérifier les conditions d'admission..."
 *     ),
 *     @OA\Property(
 *         property="category",
 *         type="string",
 *         nullable=true,
 *         description="FAQ category",
 *         example="Inscription"
 *     ),
 *     @OA\Property(
 *         property="sort_order",
 *         type="integer",
 *         description="Sort order for display",
 *         example=10
 *     ),
 *     @OA\Property(
 *         property="is_active",
 *         type="boolean",
 *         description="Whether the FAQ item is active",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp",
 *         example="2025-06-19T15:30:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last update timestamp",
 *         example="2025-06-19T15:30:00Z"
 *     )
 * )
 */
class FaqItemSchema
{
}

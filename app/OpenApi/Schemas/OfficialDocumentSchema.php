<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="OfficialDocument",
 *     type="object",
 *     title="Official Document",
 *     description="Official document related to establishments or accreditations",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Document ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Document title",
 *         example="Décret d'habilitation 2025"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         nullable=true,
 *         description="Document description",
 *         example="Décret portant habilitation des établissements d'enseignement supérieur"
 *     ),
 *     @OA\Property(
 *         property="document_url",
 *         type="string",
 *         nullable=true,
 *         description="External URL to the document",
 *         example="https://example.com/documents/decree-2025.pdf"
 *     ),
 *     @OA\Property(
 *         property="document_type",
 *         type="string",
 *         nullable=true,
 *         description="Type of document",
 *         example="decree"
 *     ),
 *     @OA\Property(
 *         property="file_size",
 *         type="integer",
 *         nullable=true,
 *         description="File size in bytes",
 *         example=1024000
 *     ),
 *     @OA\Property(
 *         property="mime_type",
 *         type="string",
 *         nullable=true,
 *         description="MIME type of the document",
 *         example="application/pdf"
 *     ),
 *     @OA\Property(
 *         property="sort_order",
 *         type="integer",
 *         description="Sort order for display",
 *         example=0
 *     ),
 *     @OA\Property(
 *         property="reference",
 *         type="object",
 *         nullable=true,
 *         description="Related reference information",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="main_date", type="string", format="date", example="2025-01-15"),
 *         @OA\Property(property="reference_number", type="string", example="DECREE-2025-001")
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
class OfficialDocumentSchema
{
}

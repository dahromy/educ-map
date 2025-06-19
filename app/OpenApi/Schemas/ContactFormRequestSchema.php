<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ContactFormRequest",
 *     type="object",
 *     title="Contact Form Request",
 *     description="Contact form submission data",
 *     required={"name", "email", "subject", "message"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         maxLength=255,
 *         description="Contact person name",
 *         example="John Doe"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         maxLength=255,
 *         description="Contact email address",
 *         example="john@example.com"
 *     ),
 *     @OA\Property(
 *         property="subject",
 *         type="string",
 *         maxLength=255,
 *         description="Message subject",
 *         example="Information Request"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         maxLength=2000,
 *         description="Message content",
 *         example="I would like more information about your programs."
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         maxLength=20,
 *         nullable=true,
 *         description="Contact phone number",
 *         example="+261 34 12 345 67"
 *     ),
 *     @OA\Property(
 *         property="organization",
 *         type="string",
 *         maxLength=255,
 *         nullable=true,
 *         description="Contact organization",
 *         example="University of Madagascar"
 *     )
 * )
 */
class ContactFormRequestSchema
{
}

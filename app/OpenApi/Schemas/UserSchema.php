<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User model with role-based access control",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(
 *         property="roles",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/UserRole"),
 *         description="User roles determining access permissions. Available roles: ROLE_USER (regular user), ROLE_ADMIN (administrator with full access), ROLE_ESTABLISHMENT (institution user with limited access to own establishment)",
 *         example={"ROLE_USER"}
 *     ),
 *     @OA\Property(property="associated_establishment_id", type="integer", nullable=true, description="ID of associated establishment (required for ROLE_ESTABLISHMENT users)", example=null),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class UserSchema
{
    // This is just a placeholder class for OpenAPI annotations
}

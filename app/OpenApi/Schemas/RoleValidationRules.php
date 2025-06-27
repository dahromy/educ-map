<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="RoleValidationRules",
 *     title="Role Validation Rules",
 *     description="Detailed validation rules and access control patterns for each role",
 *     type="object",
 *     @OA\Property(
 *         property="role_hierarchy",
 *         type="object",
 *         description="Role inheritance and permission hierarchy",
 *         @OA\Property(
 *             property="ROLE_USER",
 *             type="object",
 *             @OA\Property(
 *                 property="level",
 *                 type="integer",
 *                 example=1,
 *                 description="Base level role"
 *             ),
 *             @OA\Property(
 *                 property="inherits_from",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={},
 *                 description="No inheritance - base role"
 *             )
 *         ),
 *         @OA\Property(
 *             property="ROLE_ESTABLISHMENT",
 *             type="object",
 *             @OA\Property(
 *                 property="level",
 *                 type="integer",
 *                 example=2,
 *                 description="Enhanced user role with specific permissions"
 *             ),
 *             @OA\Property(
 *                 property="inherits_from",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={"ROLE_USER"},
 *                 description="Inherits all ROLE_USER permissions"
 *             )
 *         ),
 *         @OA\Property(
 *             property="ROLE_ADMIN",
 *             type="object",
 *             @OA\Property(
 *                 property="level",
 *                 type="integer",
 *                 example=3,
 *                 description="Highest level role with full system access"
 *             ),
 *             @OA\Property(
 *                 property="inherits_from",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={"ROLE_USER", "ROLE_ESTABLISHMENT"},
 *                 description="Inherits all permissions from lower roles"
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="validation_patterns",
 *         type="object",
 *         description="Common validation patterns used across the API",
 *         @OA\Property(
 *             property="authentication_required",
 *             type="object",
 *             @OA\Property(
 *                 property="middleware",
 *                 type="string",
 *                 example="auth:sanctum"
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 example="Requires valid Bearer token in Authorization header"
 *             )
 *         ),
 *         @OA\Property(
 *             property="role_validation",
 *             type="object",
 *             @OA\Property(
 *                 property="middleware",
 *                 type="string",
 *                 example="role:ROLE_ADMIN"
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 example="Requires user to have specific role in their roles array"
 *             )
 *         ),
 *         @OA\Property(
 *             property="ownership_validation",
 *             type="object",
 *             @OA\Property(
 *                 property="policy",
 *                 type="string",
 *                 example="EstablishmentPolicy@update"
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 example="Uses Laravel Policy to check if user can modify specific resource"
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="common_response_codes",
 *         type="object",
 *         description="Standard HTTP response codes for role-based access",
 *         @OA\Property(
 *             property="401",
 *             type="object",
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 example="Unauthorized - Missing or invalid authentication token"
 *             ),
 *             @OA\Property(
 *                 property="body_example",
 *                 type="object",
 *                 @OA\Property(property="message", type="string", example="Unauthenticated.")
 *             )
 *         ),
 *         @OA\Property(
 *             property="403",
 *             type="object",
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 example="Forbidden - User lacks required role or permissions"
 *             ),
 *             @OA\Property(
 *                 property="body_example",
 *                 type="object",
 *                 @OA\Property(property="message", type="string", example="Unauthorized. Insufficient permissions.")
 *             )
 *         ),
 *         @OA\Property(
 *             property="404",
 *             type="object",
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 example="Not Found - Resource doesn't exist or user cannot access it"
 *             ),
 *             @OA\Property(
 *                 property="body_example",
 *                 type="object",
 *                 @OA\Property(property="message", type="string", example="Resource not found.")
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="role_assignment_rules",
 *         type="object",
 *         description="Rules for how roles are assigned and managed",
 *         @OA\Property(
 *             property="ROLE_USER",
 *             type="object",
 *             @OA\Property(
 *                 property="assignment",
 *                 type="string",
 *                 example="Default role for all registered users"
 *             ),
 *             @OA\Property(
 *                 property="required_fields",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={"name", "email", "password"}
 *             )
 *         ),
 *         @OA\Property(
 *             property="ROLE_ESTABLISHMENT",
 *             type="object",
 *             @OA\Property(
 *                 property="assignment",
 *                 type="string",
 *                 example="Assigned by admin to institution representatives"
 *             ),
 *             @OA\Property(
 *                 property="required_fields",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={"name", "email", "password", "associated_establishment"}
 *             ),
 *             @OA\Property(
 *                 property="validation",
 *                 type="string",
 *                 example="associated_establishment must reference valid establishment ID"
 *             )
 *         ),
 *         @OA\Property(
 *             property="ROLE_ADMIN",
 *             type="object",
 *             @OA\Property(
 *                 property="assignment",
 *                 type="string",
 *                 example="Assigned manually by existing admin or during system setup"
 *             ),
 *             @OA\Property(
 *                 property="restrictions",
 *                 type="string",
 *                 example="Cannot be self-assigned; requires existing admin privileges"
 *             )
 *         )
 *     )
 * )
 */
class RoleValidationRules
{
    // This is just a placeholder class for OpenAPI annotations
}

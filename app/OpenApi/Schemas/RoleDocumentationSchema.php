<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="RoleDocumentation",
 *     title="Role Documentation",
 *     description="Comprehensive documentation for the role-based access control system",
 *     type="object",
 *     @OA\Property(
 *         property="available_roles",
 *         type="object",
 *         description="Available user roles and their permissions",
 *         @OA\Property(
 *             property="ROLE_USER",
 *             type="object",
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 example="Regular User"
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 example="Standard users with basic read access to public data"
 *             ),
 *             @OA\Property(
 *                 property="permissions",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={
 *                     "View establishments and their details",
 *                     "Search establishments with filters",
 *                     "Access map data and markers",
 *                     "Compare establishments",
 *                     "Save and manage search history (when authenticated)",
 *                     "Update own user profile",
 *                     "Access FAQ and official documents"
 *                 }
 *             )
 *         ),
 *         @OA\Property(
 *             property="ROLE_ADMIN",
 *             type="object",
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 example="Administrator"
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 example="System administrators with full access to all functionality"
 *             ),
 *             @OA\Property(
 *                 property="permissions",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={
 *                     "All ROLE_USER permissions",
 *                     "Create, update, and delete establishments",
 *                     "Manage all entities (categories, domains, grades, mentions, labels, etc.)",
 *                     "Create, update, and delete references and accreditations",
 *                     "Manage FAQ items and official documents",
 *                     "Access admin statistics and analytics",
 *                     "Export data in various formats",
 *                     "Manage user accounts and assign roles",
 *                     "Full system administration"
 *                 }
 *             )
 *         ),
 *         @OA\Property(
 *             property="ROLE_ESTABLISHMENT",
 *             type="object",
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 example="Establishment User"
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 example="Institution representatives with limited access to manage their own establishment data"
 *             ),
 *             @OA\Property(
 *                 property="permissions",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={
 *                     "All ROLE_USER permissions",
 *                     "Update own establishment information (address, contact details, indicators)",
 *                     "Manage own establishment profile and logo",
 *                     "Update establishment description and basic information",
 *                     "View own establishment statistics"
 *                 }
 *             ),
 *             @OA\Property(
 *                 property="restrictions",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={
 *                     "Can only modify their associated establishment (linked via associated_establishment field)",
 *                     "Cannot create or delete establishments",
 *                     "Cannot manage system-wide entities",
 *                     "Cannot access admin statistics",
 *                     "Cannot export data"
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="authentication_requirements",
 *         type="object",
 *         @OA\Property(
 *             property="public_endpoints",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "GET /api/establishments",
 *                 "GET /api/establishments/{id}",
 *                 "GET /api/map/markers",
 *                 "GET /api/compare",
 *                 "GET /api/domains",
 *                 "GET /api/categories",
 *                 "GET /api/faq_items",
 *                 "GET /api/official_documents",
 *                 "POST /api/contact"
 *             }
 *         ),
 *         @OA\Property(
 *             property="authenticated_endpoints",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "GET /api/me",
 *                 "PUT /api/me",
 *                 "POST /api/me/searches",
 *                 "GET /api/me/searches",
 *                 "DELETE /api/me/searches/{id}"
 *             }
 *         ),
 *         @OA\Property(
 *             property="admin_only_endpoints",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "POST /api/establishments",
 *                 "DELETE /api/establishments/{id}",
 *                 "All CRUD operations on /api/categories, /api/domains, etc.",
 *                 "GET /api/admin/stats/*",
 *                 "GET /api/admin/export/*"
 *             }
 *         ),
 *         @OA\Property(
 *             property="establishment_user_endpoints",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "PUT /api/establishments/{id} (own establishment only)"
 *             }
 *         )
 *     )
 * )
 */
class RoleDocumentationSchema
{
    // This is just a placeholder class for OpenAPI annotations
}

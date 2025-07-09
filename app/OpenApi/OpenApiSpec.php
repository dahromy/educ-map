<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Educ-Map API Documentation",
 *     description="API for higher education institutions directory in Madagascar",
 *     @OA\Contact(
 *         email="contact@educ-map.mg",
 *         name="Educ-Map Support"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Educ-Map API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     description="Laravel Sanctum token authentication. Role-based access control system with three main roles: ROLE_USER (regular users with read access and search history), ROLE_ADMIN (full system access including all CRUD operations and admin functions), ROLE_ESTABLISHMENT (institution users with limited access to manage their own establishment data only)"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints for user authentication and profile management"
 * )
 * @OA\Tag(
 *     name="User Management",
 *     description="Admin endpoints for managing users"
 * )
 * @OA\Tag(
 *     name="Authorization & Roles",
 *     description="Role-based access control documentation and permissions"
 * )
 * @OA\Tag(
 *     name="Categories",
 *     description="Endpoints for accessing and managing educational institution categories"
 * )
 * @OA\Tag(
 *     name="Establishments",
 *     description="Endpoints for accessing and managing establishment data"
 * )
 * @OA\Tag(
 *     name="Lists",
 *     description="Endpoints for accessing simple list data for filters and forms"
 * )
 * @OA\Tag(
 *     name="Map & Comparison",
 *     description="Endpoints for map markers and establishment comparison"
 * )
 * @OA\Tag(
 *     name="Search History",
 *     description="Endpoints for managing user search history"
 * )
 * @OA\Tag(
 *     name="FAQ",
 *     description="Endpoints for managing FAQ items"
 * )
 * @OA\Tag(
 *     name="Official Documents",
 *     description="Endpoints for accessing official documents"
 * )
 * @OA\Tag(
 *     name="Contact",
 *     description="Contact form submission endpoint"
 * )
 * @OA\Tag(
 *     name="Admin Statistics",
 *     description="Statistics endpoints for administrators"
 * )
 * @OA\Tag(
 *     name="Export",
 *     description="Export endpoints for data download"
 * )
 *
 * @OA\Components(
 *     @OA\Schema(
 *         schema="RolePermissions",
 *         title="Role Permissions",
 *         description="Complete role-based access control system documentation",
 *         type="object",
 *         @OA\Property(
 *             property="ROLE_USER",
 *             type="object",
 *             description="Regular user permissions - authenticated users with basic access",
 *             @OA\Property(property="description", type="string", example="Regular users with authenticated access to public data and personal features"),
 *             @OA\Property(
 *                 property="permissions",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={
 *                     "View all establishments and their details",
 *                     "Search establishments with advanced filters",
 *                     "Access map data and markers",
 *                     "Compare multiple establishments",
 *                     "Save and manage personal search history",
 *                     "Update own user profile",
 *                     "Access FAQ and official documents",
 *                     "Submit contact form"
 *                 }
 *             ),
 *             @OA\Property(
 *                 property="endpoints",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={
 *                     "GET /api/me",
 *                     "PUT /api/me",
 *                     "POST /api/logout",
 *                     "GET /api/me/searches",
 *                     "POST /api/me/searches",
 *                     "DELETE /api/me/searches/{id}",
 *                     "All public endpoints"
 *                 }
 *             )
 *         ),
 *         @OA\Property(
 *             property="ROLE_ADMIN",
 *             type="object",
 *             description="Administrator permissions - full system access",
 *             @OA\Property(property="description", type="string", example="System administrators with complete access to all functionality including CRUD operations and admin tools"),
 *             @OA\Property(
 *                 property="permissions",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={
 *                     "All ROLE_USER permissions",
 *                     "Create, update, and delete establishments",
 *                     "Manage all system entities (categories, domains, grades, mentions, labels)",
 *                     "Create, update, and delete references and accreditations",
 *                     "Manage FAQ items and official documents",
 *                     "Access comprehensive admin statistics and analytics",
 *                     "Export data in multiple formats (CSV, JSON)",
 *                     "Manage user accounts and assign roles",
 *                     "Full system configuration and maintenance"
 *                 }
 *             ),
 *             @OA\Property(
 *                 property="endpoints",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={
 *                     "All ROLE_USER endpoints",
 *                     "POST /api/establishments",
 *                     "DELETE /api/establishments/{id}",
 *                     "POST|PUT|DELETE /api/categories",
 *                     "POST|PUT|DELETE /api/domains",
 *                     "POST|PUT|DELETE /api/grades",
 *                     "POST|PUT|DELETE /api/mentions",
 *                     "POST|PUT|DELETE /api/labels",
 *                     "POST|PUT|DELETE /api/admin/faq",
 *                     "GET /api/admin/stats/*",
 *                     "GET /api/admin/export/*"
 *                 }
 *             )
 *         ),
 *         @OA\Property(
 *             property="ROLE_ESTABLISHMENT",
 *             type="object",
 *             description="Establishment user permissions - limited access to own institution data",
 *             @OA\Property(property="description", type="string", example="Institution representatives with controlled access to manage only their own establishment data"),
 *             @OA\Property(
 *                 property="permissions",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={
 *                     "All ROLE_USER permissions",
 *                     "Update own establishment information (address, contact details)",
 *                     "Manage own establishment profile and logo",
 *                     "Update establishment description and indicators",
 *                     "View own establishment statistics",
 *                     "Manage own establishment departments and programs"
 *                 }
 *             ),
 *             @OA\Property(
 *                 property="endpoints",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={
 *                     "All ROLE_USER endpoints",
 *                     "PUT|PATCH /api/establishments/{id} (own establishment only)"
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
 *                     "Cannot access admin statistics or export functions",
 *                     "Cannot manage other users or assign roles"
 *                 }
 *             )
 *         ),
 *         @OA\Property(
 *             property="public_access",
 *             type="object",
 *             description="Public endpoints accessible without authentication",
 *             @OA\Property(
 *                 property="endpoints",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={
 *                     "POST /api/login",
 *                     "GET /api/establishments",
 *                     "GET /api/establishments/{id}",
 *                     "GET /api/establishments/recent",
 *                     "GET /api/map/markers",
 *                     "GET /api/compare",
 *                     "GET /api/categories",
 *                     "GET /api/domains",
 *                     "GET /api/grades",
 *                     "GET /api/mentions",
 *                     "GET /api/labels",
 *                     "GET /api/lists",
 *                     "GET /api/faq",
 *                     "GET /api/documents",
 *                     "POST /api/contact"
 *                 }
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/search",
 *     operationId="globalSearch",
 *     tags={"Establishments"},
 *     summary="Global search for establishments with advanced filters",
 *     description="Performs a global search on establishments using various filters and returns a paginated list.",
 *     @OA\Parameter(name="q", in="query", required=false, description="Full-text search across establishment fields", @OA\Schema(type="string")),
 *     @OA\Parameter(name="name", in="query", required=false, description="Partial match on establishment name", @OA\Schema(type="string")),
 *     @OA\Parameter(name="abbreviation", in="query", required=false, description="Partial match on abbreviation", @OA\Schema(type="string")),
 *     @OA\Parameter(name="description", in="query", required=false, description="Partial match on description", @OA\Schema(type="string")),
 *     @OA\Parameter(name="region", in="query", required=false, description="Exact match on region", @OA\Schema(type="string")),
 *     @OA\Parameter(name="city", in="query", required=false, description="Exact match on city", @OA\Schema(type="string")),
 *     @OA\Parameter(name="address", in="query", required=false, description="Partial match on address", @OA\Schema(type="string")),
 *     @OA\Parameter(name="category_id", in="query", required=false, description="Filter by category ID", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="category_name", in="query", required=false, description="Filter by category name", @OA\Schema(type="string")),
 *     @OA\Parameter(name="status", in="query", required=false, description="Filter by status", @OA\Schema(type="string")),
 *     @OA\Parameter(name="domain_id", in="query", required=false, description="Filter by domain ID", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="domain_name", in="query", required=false, description="Filter by domain name", @OA\Schema(type="string")),
 *     @OA\Parameter(name="grade_id", in="query", required=false, description="Filter by grade ID", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="grade_name", in="query", required=false, description="Filter by grade name", @OA\Schema(type="string")),
 *     @OA\Parameter(name="mention_id", in="query", required=false, description="Filter by mention ID", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="mention_name", in="query", required=false, description="Filter by mention name", @OA\Schema(type="string")),
 *     @OA\Parameter(name="label_id", in="query", required=false, description="Filter by label ID", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="label_name", in="query", required=false, description="Filter by label name", @OA\Schema(type="string")),
 *     @OA\Parameter(name="student_count_min", in="query", required=false, description="Minimum student count", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="student_count_max", in="query", required=false, description="Maximum student count", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="success_rate_min", in="query", required=false, description="Minimum success rate", @OA\Schema(type="number", format="float")),
 *     @OA\Parameter(name="success_rate_max", in="query", required=false, description="Maximum success rate", @OA\Schema(type="number", format="float")),
 *     @OA\Parameter(name="page", in="query", required=false, description="Page number for pagination", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="per_page", in="query", required=false, description="Number of results per page", @OA\Schema(type="integer")),
 *     @OA\Response(
 *         response=200,
 *         description="Paginated list of establishments matching search criteria",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/EstablishmentResource")),
 *             @OA\Property(property="meta", type="object", description="Pagination metadata")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Invalid request parameters"),
 *     @OA\Response(response=500, description="Server error")
 * )
 */
class OpenApiSpec
{
    // This is just a placeholder class for OpenAPI annotations
}

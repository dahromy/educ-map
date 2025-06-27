<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="EndpointRoleMapping",
 *     title="Endpoint Role Mapping",
 *     description="Complete mapping of API endpoints to required roles and permissions",
 *     type="object",
 *     @OA\Property(
 *         property="public_endpoints",
 *         type="object",
 *         description="Endpoints accessible without authentication",
 *         @OA\Property(
 *             property="authentication",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={"POST /api/login"}
 *         ),
 *         @OA\Property(
 *             property="establishments",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "GET /api/establishments",
 *                 "GET /api/establishments/{id}",
 *                 "GET /api/establishments/recent"
 *             }
 *         ),
 *         @OA\Property(
 *             property="map_and_comparison",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "GET /api/map/markers",
 *                 "GET /api/compare"
 *             }
 *         ),
 *         @OA\Property(
 *             property="reference_data",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "GET /api/categories",
 *                 "GET /api/domains",
 *                 "GET /api/grades",
 *                 "GET /api/mentions",
 *                 "GET /api/labels",
 *                 "GET /api/lists"
 *             }
 *         ),
 *         @OA\Property(
 *             property="content_and_support",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "GET /api/faq",
 *                 "GET /api/faq/{id}",
 *                 "GET /api/documents",
 *                 "GET /api/documents/{id}",
 *                 "POST /api/contact"
 *             }
 *         )
 *     ),
 *     @OA\Property(
 *         property="authenticated_endpoints",
 *         type="object",
 *         description="Endpoints requiring authentication (ROLE_USER minimum)",
 *         @OA\Property(
 *             property="user_profile",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "GET /api/me",
 *                 "PUT /api/me",
 *                 "POST /api/logout"
 *             }
 *         ),
 *         @OA\Property(
 *             property="search_history",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "GET /api/me/searches",
 *                 "POST /api/me/searches",
 *                 "DELETE /api/me/searches/{id}"
 *             }
 *         )
 *     ),
 *     @OA\Property(
 *         property="admin_only_endpoints",
 *         type="object",
 *         description="Endpoints requiring ROLE_ADMIN",
 *         @OA\Property(
 *             property="establishment_management",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "POST /api/establishments",
 *                 "DELETE /api/establishments/{id}"
 *             }
 *         ),
 *         @OA\Property(
 *             property="entity_management",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "POST /api/categories",
 *                 "PUT /api/categories/{id}",
 *                 "DELETE /api/categories/{id}",
 *                 "POST /api/domains",
 *                 "PUT /api/domains/{id}",
 *                 "DELETE /api/domains/{id}",
 *                 "POST /api/grades",
 *                 "PUT /api/grades/{id}",
 *                 "DELETE /api/grades/{id}",
 *                 "POST /api/mentions",
 *                 "PUT /api/mentions/{id}",
 *                 "DELETE /api/mentions/{id}",
 *                 "POST /api/labels",
 *                 "PUT /api/labels/{id}",
 *                 "DELETE /api/labels/{id}"
 *             }
 *         ),
 *         @OA\Property(
 *             property="content_management",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "POST /api/admin/faq",
 *                 "PUT /api/admin/faq/{id}",
 *                 "DELETE /api/admin/faq/{id}"
 *             }
 *         ),
 *         @OA\Property(
 *             property="statistics_and_analytics",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "GET /api/admin/stats/overview",
 *                 "GET /api/admin/stats/establishments-by-category",
 *                 "GET /api/admin/stats/geographical-distribution",
 *                 "GET /api/admin/stats/habilitations-by-year",
 *                 "GET /api/admin/stats/users-by-role",
 *                 "GET /api/admin/stats/recent-activity"
 *             }
 *         ),
 *         @OA\Property(
 *             property="data_export",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "GET /api/admin/export/establishments.csv",
 *                 "GET /api/admin/export/establishments.json"
 *             }
 *         )
 *     ),
 *     @OA\Property(
 *         property="establishment_user_endpoints",
 *         type="object",
 *         description="Endpoints requiring ROLE_ESTABLISHMENT with ownership validation",
 *         @OA\Property(
 *             property="own_establishment_management",
 *             type="array",
 *             @OA\Items(type="string"),
 *             example={
 *                 "PUT /api/establishments/{id}",
 *                 "PATCH /api/establishments/{id}"
 *             }
 *         ),
 *         @OA\Property(
 *             property="ownership_validation",
 *             type="string",
 *             example="User can only modify establishments where user.associated_establishment equals the establishment ID"
 *         )
 *     ),
 *     @OA\Property(
 *         property="flexible_access_endpoints",
 *         type="object",
 *         description="Endpoints with role-based access variations",
 *         @OA\Property(
 *             property="establishment_updates",
 *             type="object",
 *             @OA\Property(
 *                 property="endpoint",
 *                 type="string",
 *                 example="PUT|PATCH /api/establishments/{id}"
 *             ),
 *             @OA\Property(
 *                 property="access_rules",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 example={
 *                     "ROLE_ADMIN: Can update any establishment",
 *                     "ROLE_ESTABLISHMENT: Can only update own associated establishment"
 *                 }
 *             )
 *         )
 *     )
 * )
 */
class EndpointRoleMapping
{
    // This is just a placeholder class for OpenAPI annotations
}

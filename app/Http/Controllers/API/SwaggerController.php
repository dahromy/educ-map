<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Educ-Map API Documentation",
 *     version="1.0.0",
 *     description="API documentation for Educ-Map - Higher Education Institutions Directory in Madagascar",
 *     @OA\Contact(
 *         email="contact@educ-map.mg",
 *         name="Educ-Map Support Team"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     description="Educ-Map API Server",
 *     url=L5_SWAGGER_CONST_HOST
 * )
 *
 * @OA\PathItem(
 *     path="/api"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="API endpoints for user authentication"
 * )
 * 
 * @OA\Tag(
 *     name="Establishments",
 *     description="API endpoints for establishments"
 * )
 * 
 * @OA\Tag(
 *     name="Map",
 *     description="API endpoints for map markers"
 * )
 * 
 * @OA\Tag(
 *     name="Comparison",
 *     description="API endpoints for comparing establishments"
 * )
 * 
 * @OA\Tag(
 *     name="Search History",
 *     description="API endpoints for managing user search history"
 * )
 */
class SwaggerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/establishments",
     *     summary="Get list of establishments",
     *     description="Returns paginated list of establishments with optional filtering",
     *     operationId="getEstablishmentsList",
     *     tags={"Establishments"},
     *     @OA\Parameter(
     *         name="region",
     *         in="query",
     *         description="Filter by region",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter by establishment name (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="abbreviation",
     *         in="query",
     *         description="Filter by establishment abbreviation (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="domain",
     *         in="query",
     *         description="Filter by domain name (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="label",
     *         in="query",
     *         description="Filter by label name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="reference_start_date",
     *         in="query",
     *         description="Filter by reference date range (start)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="reference_end_date",
     *         in="query",
     *         description="Filter by reference date range (end)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by (name, student_count, reference_date)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"name", "student_count", "reference_date"}, default="name")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction (asc or desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15, minimum=1, maximum=100)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/EstablishmentResource")),
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/establishments/{id}",
     *     summary="Get establishment details",
     *     description="Returns detailed information for a specific establishment",
     *     operationId="getEstablishmentDetail",
     *     tags={"Establishments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Establishment ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/EstablishmentDetailResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Establishment not found"
     *     )
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/establishments/recent",
     *     summary="Get recent establishments",
     *     description="Returns recently added establishments or those with recent accreditations",
     *     operationId="getRecentEstablishments",
     *     tags={"Establishments"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/EstablishmentResource")
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/map/markers",
     *     summary="Get map markers",
     *     description="Returns lightweight establishment data for map markers",
     *     operationId="getMapMarkers",
     *     tags={"Map"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/EstablishmentMapResource")
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/compare",
     *     summary="Compare establishments",
     *     description="Compare multiple establishments side by side",
     *     operationId="compareEstablishments",
     *     tags={"Comparison"},
     *     @OA\Parameter(
     *         name="ids[]",
     *         in="query",
     *         description="List of establishment IDs to compare",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(type="array", @OA\Items(type="integer"), minItems=2, maxItems=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/EstablishmentComparisonResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="One or more establishments not found"
     *     )
     * )
     */

    /**
     * @OA\Post(
     *     path="/api/establishments",
     *     summary="Create new establishment",
     *     description="Creates a new establishment (admin access required)",
     *     operationId="storeEstablishment",
     *     tags={"Establishments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreEstablishmentRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Establishment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/EstablishmentDetailResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Admin access required"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    /**
     * @OA\Put(
     *     path="/api/establishments/{id}",
     *     summary="Update establishment",
     *     description="Updates an existing establishment (admin or establishment owner access required)",
     *     operationId="updateEstablishment",
     *     tags={"Establishments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Establishment ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateEstablishmentRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Establishment updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/EstablishmentDetailResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Admin or establishment owner access required"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Establishment not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    /**
     * @OA\Delete(
     *     path="/api/establishments/{id}",
     *     summary="Delete establishment",
     *     description="Deletes an establishment (admin access required)",
     *     operationId="deleteEstablishment",
     *     tags={"Establishments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Establishment ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Establishment deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Admin access required"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Establishment not found"
     *     )
     * )
     */

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     description="Creates a new user account",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="1|abcdefghijklmnopqrstuvwxyz")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login user",
     *     description="Authenticates user and returns token",
     *     operationId="loginUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@educ-map.mg"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="1|abcdefghijklmnopqrstuvwxyz")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Get authenticated user profile",
     *     description="Returns the currently authenticated user's details",
     *     operationId="getUserProfile",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */

    /**
     * @OA\Put(
     *     path="/api/me",
     *     summary="Update user profile",
     *     description="Updates the currently authenticated user's profile",
     *     operationId="updateUserProfile",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Name"),
     *             @OA\Property(property="email", type="string", format="email", example="updated@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user",
     *     description="Invalidates the current authentication token",
     *     operationId="logoutUser",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/me/searches",
     *     summary="Get user search history",
     *     description="Returns the authenticated user's saved searches",
     *     operationId="getUserSearchHistory",
     *     tags={"Search History"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SearchHistory")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */

    /**
     * @OA\Post(
     *     path="/api/me/searches",
     *     summary="Save search",
     *     description="Saves a search query and filters for the authenticated user",
     *     operationId="saveSearch",
     *     tags={"Search History"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="search_query_text", type="string", example="computer science"),
     *             @OA\Property(
     *                 property="search_filters",
     *                 type="object",
     *                 example={"region": "Analamanga", "category": "Public University"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Search saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Search saved successfully"),
     *             @OA\Property(property="search", ref="#/components/schemas/SearchHistory")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    /**
     * @OA\Delete(
     *     path="/api/me/searches/{id}",
     *     summary="Delete saved search",
     *     description="Deletes a saved search for the authenticated user",
     *     operationId="deleteSearch",
     *     tags={"Search History"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Search history ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Search deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - not the owner of this search"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Search not found"
     *     )
     * )
     */
}

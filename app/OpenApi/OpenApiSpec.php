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
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints for user authentication and profile management"
 * )
 * @OA\Tag(
 *     name="Establishments",
 *     description="Endpoints for accessing and managing establishment data"
 * )
 * @OA\Tag(
 *     name="Map & Comparison",
 *     description="Endpoints for map markers and establishment comparison"
 * )
 * @OA\Tag(
 *     name="Search History",
 *     description="Endpoints for managing user search history"
 * )
 */
class OpenApiSpec
{
    // This is just a placeholder class for OpenAPI annotations
}

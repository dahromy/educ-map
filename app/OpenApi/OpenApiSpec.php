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
 */
class OpenApiSpec
{
    // This is just a placeholder class for OpenAPI annotations
}

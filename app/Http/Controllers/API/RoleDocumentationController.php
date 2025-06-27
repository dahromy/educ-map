<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class RoleDocumentationController extends Controller
{
    /**
     * Get role documentation and permissions.
     *
     * This endpoint provides comprehensive documentation about the role-based
     * access control system used throughout the API.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/roles/documentation",
     *     summary="Get role-based access control documentation",
     *     description="Returns comprehensive documentation about available user roles, their permissions, and access restrictions. This is a documentation endpoint that explains the RBAC system.",
     *     tags={"Authorization & Roles"},
     *     @OA\Response(
     *         response=200,
     *         description="Role documentation retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/RoleDocumentation")
     *     )
     * )
     */
    public function documentation(): JsonResponse
    {
        return response()->json([
            'available_roles' => [
                'ROLE_USER' => [
                    'name' => 'Regular User',
                    'level' => 1,
                    'description' => 'Standard authenticated users with basic read access to public data and personal features',
                    'permissions' => [
                        'View all establishments and their details',
                        'Search establishments with advanced filters',
                        'Access map data and markers',
                        'Compare multiple establishments',
                        'Save and manage personal search history',
                        'Update own user profile',
                        'Access FAQ and official documents',
                        'Submit contact form'
                    ],
                    'endpoints' => [
                        'GET /api/me',
                        'PUT /api/me',
                        'POST /api/logout',
                        'GET /api/me/searches',
                        'POST /api/me/searches',
                        'DELETE /api/me/searches/{id}',
                        'All public endpoints'
                    ]
                ],
                'ROLE_ADMIN' => [
                    'name' => 'Administrator',
                    'level' => 3,
                    'description' => 'System administrators with complete access to all functionality including CRUD operations and admin tools',
                    'inherits_from' => ['ROLE_USER', 'ROLE_ESTABLISHMENT'],
                    'permissions' => [
                        'All ROLE_USER permissions',
                        'Create, update, and delete establishments',
                        'Manage all system entities (categories, domains, grades, mentions, labels)',
                        'Create, update, and delete references and accreditations',
                        'Manage FAQ items and official documents',
                        'Access comprehensive admin statistics and analytics',
                        'Export data in multiple formats (CSV, JSON)',
                        'Manage user accounts and assign roles',
                        'Full system configuration and maintenance'
                    ],
                    'endpoints' => [
                        'All ROLE_USER endpoints',
                        'POST /api/establishments',
                        'DELETE /api/establishments/{id}',
                        'POST|PUT|DELETE /api/categories',
                        'POST|PUT|DELETE /api/domains',
                        'POST|PUT|DELETE /api/grades',
                        'POST|PUT|DELETE /api/mentions',
                        'POST|PUT|DELETE /api/labels',
                        'POST|PUT|DELETE /api/admin/faq',
                        'GET /api/admin/stats/*',
                        'GET /api/admin/export/*'
                    ]
                ],
                'ROLE_ESTABLISHMENT' => [
                    'name' => 'Establishment User',
                    'level' => 2,
                    'description' => 'Institution representatives with controlled access to manage only their own establishment data',
                    'inherits_from' => ['ROLE_USER'],
                    'permissions' => [
                        'All ROLE_USER permissions',
                        'Update own establishment information (address, contact details)',
                        'Manage own establishment profile and logo',
                        'Update establishment description and indicators',
                        'View own establishment statistics',
                        'Manage own establishment departments and programs'
                    ],
                    'endpoints' => [
                        'All ROLE_USER endpoints',
                        'PUT|PATCH /api/establishments/{id} (own establishment only)'
                    ],
                    'restrictions' => [
                        'Can only modify their associated establishment (linked via associated_establishment field)',
                        'Cannot create or delete establishments',
                        'Cannot manage system-wide entities',
                        'Cannot access admin statistics or export functions',
                        'Cannot manage other users or assign roles'
                    ],
                    'ownership_validation' => 'User can only modify establishments where user.associated_establishment equals the establishment ID'
                ]
            ],
            'access_control_patterns' => [
                'public_endpoints' => [
                    'description' => 'Accessible without authentication',
                    'endpoints' => [
                        'POST /api/login',
                        'GET /api/establishments',
                        'GET /api/establishments/{id}',
                        'GET /api/establishments/recent',
                        'GET /api/map/markers',
                        'GET /api/compare',
                        'GET /api/categories',
                        'GET /api/domains',
                        'GET /api/grades',
                        'GET /api/mentions',
                        'GET /api/labels',
                        'GET /api/lists',
                        'GET /api/faq',
                        'GET /api/documents',
                        'POST /api/contact'
                    ]
                ],
                'authenticated_endpoints' => [
                    'description' => 'Require valid Bearer token (minimum ROLE_USER)',
                    'middleware' => 'auth:sanctum',
                    'endpoints' => [
                        'GET /api/me',
                        'PUT /api/me',
                        'POST /api/logout',
                        'GET /api/me/searches',
                        'POST /api/me/searches',
                        'DELETE /api/me/searches/{id}'
                    ]
                ],
                'admin_only_endpoints' => [
                    'description' => 'Require ROLE_ADMIN',
                    'middleware' => ['auth:sanctum', 'role:ROLE_ADMIN'],
                    'endpoints' => [
                        'POST /api/establishments',
                        'DELETE /api/establishments/{id}',
                        'All CRUD operations on reference entities',
                        'All admin statistics endpoints',
                        'All export endpoints',
                        'Admin FAQ management'
                    ]
                ],
                'conditional_access_endpoints' => [
                    'description' => 'Role-based access with additional validation',
                    'patterns' => [
                        'establishment_updates' => [
                            'endpoint' => 'PUT|PATCH /api/establishments/{id}',
                            'access_rules' => [
                                'ROLE_ADMIN: Can update any establishment',
                                'ROLE_ESTABLISHMENT: Can only update own associated establishment'
                            ]
                        ]
                    ]
                ]
            ],
            'validation_rules' => [
                'authentication' => [
                    'method' => 'Bearer token in Authorization header',
                    'middleware' => 'auth:sanctum',
                    'token_source' => 'Laravel Sanctum personal access tokens'
                ],
                'role_checking' => [
                    'method' => 'Check roles array in user model',
                    'middleware' => 'role:{role_name}',
                    'implementation' => 'User::hasRole() method checks if role exists in user.roles array'
                ],
                'ownership_validation' => [
                    'method' => 'Laravel Policies',
                    'implementation' => 'EstablishmentPolicy checks user.associated_establishment against resource ID'
                ]
            ],
            'response_codes' => [
                '200' => 'Success - Request completed successfully',
                '201' => 'Created - Resource created successfully (admin operations)',
                '401' => 'Unauthorized - Missing or invalid authentication token',
                '403' => 'Forbidden - User lacks required role or permissions',
                '404' => 'Not Found - Resource doesn\'t exist or user cannot access it',
                '422' => 'Unprocessable Entity - Validation errors in request data',
                '500' => 'Internal Server Error - Server-side error'
            ],
            'role_management' => [
                'assignment_rules' => [
                    'ROLE_USER' => 'Default role for all registered users',
                    'ROLE_ESTABLISHMENT' => 'Assigned by admin to institution representatives',
                    'ROLE_ADMIN' => 'Assigned manually by existing admin or during system setup'
                ],
                'required_fields' => [
                    'ROLE_USER' => ['name', 'email', 'password'],
                    'ROLE_ESTABLISHMENT' => ['name', 'email', 'password', 'associated_establishment'],
                    'ROLE_ADMIN' => ['name', 'email', 'password']
                ],
                'validation_notes' => [
                    'Roles are stored as array in users.roles field',
                    'associated_establishment must reference valid establishment ID for ROLE_ESTABLISHMENT users',
                    'Users can have multiple roles, but typically have one primary role',
                    'Admin role cannot be self-assigned through public registration'
                ]
            ]
        ]);
    }
}

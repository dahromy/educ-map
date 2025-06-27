<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleDocumentationApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test role documentation endpoint returns comprehensive role information.
     */
    public function test_role_documentation_returns_comprehensive_data(): void
    {
        $response = $this->get('/api/roles/documentation');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'available_roles' => [
                    'ROLE_USER' => [
                        'name',
                        'level',
                        'description',
                        'permissions',
                        'endpoints'
                    ],
                    'ROLE_ADMIN' => [
                        'name',
                        'level',
                        'description',
                        'inherits_from',
                        'permissions',
                        'endpoints'
                    ],
                    'ROLE_ESTABLISHMENT' => [
                        'name',
                        'level',
                        'description',
                        'inherits_from',
                        'permissions',
                        'endpoints',
                        'restrictions',
                        'ownership_validation'
                    ]
                ],
                'access_control_patterns' => [
                    'public_endpoints',
                    'authenticated_endpoints',
                    'admin_only_endpoints',
                    'conditional_access_endpoints'
                ],
                'validation_rules' => [
                    'authentication',
                    'role_checking',
                    'ownership_validation'
                ],
                'response_codes',
                'role_management'
            ]);
    }

    /**
     * Test role documentation contains all expected roles.
     */
    public function test_role_documentation_contains_all_roles(): void
    {
        $response = $this->get('/api/roles/documentation');

        $response->assertStatus(200)
            ->assertJsonPath('available_roles.ROLE_USER.name', 'Regular User')
            ->assertJsonPath('available_roles.ROLE_ADMIN.name', 'Administrator')
            ->assertJsonPath('available_roles.ROLE_ESTABLISHMENT.name', 'Establishment User');
    }

    /**
     * Test role documentation includes proper role hierarchy.
     */
    public function test_role_documentation_includes_role_hierarchy(): void
    {
        $response = $this->get('/api/roles/documentation');

        $response->assertStatus(200)
            ->assertJsonPath('available_roles.ROLE_USER.level', 1)
            ->assertJsonPath('available_roles.ROLE_ESTABLISHMENT.level', 2)
            ->assertJsonPath('available_roles.ROLE_ADMIN.level', 3)
            ->assertJsonPath('available_roles.ROLE_ESTABLISHMENT.inherits_from.0', 'ROLE_USER')
            ->assertJsonPath('available_roles.ROLE_ADMIN.inherits_from.0', 'ROLE_USER')
            ->assertJsonPath('available_roles.ROLE_ADMIN.inherits_from.1', 'ROLE_ESTABLISHMENT');
    }

    /**
     * Test role documentation includes endpoint access patterns.
     */
    public function test_role_documentation_includes_endpoint_patterns(): void
    {
        $response = $this->get('/api/roles/documentation');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_control_patterns' => [
                    'public_endpoints' => [
                        'description',
                        'endpoints'
                    ],
                    'authenticated_endpoints' => [
                        'description',
                        'middleware',
                        'endpoints'
                    ],
                    'admin_only_endpoints' => [
                        'description',
                        'middleware',
                        'endpoints'
                    ],
                    'conditional_access_endpoints' => [
                        'description',
                        'patterns'
                    ]
                ]
            ]);
    }

    /**
     * Test role documentation includes validation rules.
     */
    public function test_role_documentation_includes_validation_rules(): void
    {
        $response = $this->get('/api/roles/documentation');

        $response->assertStatus(200)
            ->assertJsonPath('validation_rules.authentication.middleware', 'auth:sanctum')
            ->assertJsonPath('validation_rules.role_checking.middleware', 'role:{role_name}')
            ->assertJsonPath('validation_rules.ownership_validation.method', 'Laravel Policies');
    }

    /**
     * Test role documentation includes response codes.
     */
    public function test_role_documentation_includes_response_codes(): void
    {
        $response = $this->get('/api/roles/documentation');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'response_codes' => [
                    '200',
                    '201',
                    '401',
                    '403',
                    '404',
                    '422',
                    '500'
                ]
            ]);
    }

    /**
     * Test role documentation includes role management information.
     */
    public function test_role_documentation_includes_role_management(): void
    {
        $response = $this->get('/api/roles/documentation');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'role_management' => [
                    'assignment_rules',
                    'required_fields',
                    'validation_notes'
                ]
            ])
            ->assertJsonPath('role_management.assignment_rules.ROLE_USER', 'Default role for all registered users')
            ->assertJsonPath('role_management.required_fields.ROLE_ESTABLISHMENT.3', 'associated_establishment');
    }

    /**
     * Test endpoint is accessible without authentication.
     */
    public function test_role_documentation_is_public(): void
    {
        // Test without authentication
        $response = $this->get('/api/roles/documentation');
        $response->assertStatus(200);

        // Verify it's not protected by auth middleware
        $response->assertJsonStructure(['available_roles']);
    }
}

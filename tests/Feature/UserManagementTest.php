<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Establishment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user for testing
        $this->adminUser = User::factory()->create([
            'roles' => ['ROLE_ADMIN'],
        ]);
    }

    public function test_admin_can_list_users()
    {
        // Create some test users
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'roles',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_admin_can_create_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => ['ROLE_USER'],
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/users', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'roles',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
    }

    public function test_admin_can_create_establishment_user()
    {
        $establishment = Establishment::factory()->create();

        $userData = [
            'name' => 'Institution User',
            'email' => 'institution@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => ['ROLE_ESTABLISHMENT'],
            'associated_establishment' => $establishment->id,
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/users', $userData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'institution@example.com',
            'associated_establishment' => $establishment->id,
        ]);
    }

    public function test_admin_can_update_user()
    {
        $user = User::factory()->create([
            'roles' => ['ROLE_USER'],
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => $user->email,
            'roles' => ['ROLE_USER'],
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'User deleted successfully']);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_themselves()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/users/{$this->adminUser->id}");

        $response->assertStatus(422)
            ->assertJson(['message' => 'You cannot delete your own account']);
    }

    public function test_non_admin_cannot_access_user_management()
    {
        $regularUser = User::factory()->create([
            'roles' => ['ROLE_USER'],
        ]);

        $response = $this->actingAs($regularUser, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_user_management()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    public function test_validation_errors_for_user_creation()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'roles']);
    }

    public function test_establishment_role_requires_associated_establishment()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => ['ROLE_ESTABLISHMENT'],
            // Missing associated_establishment
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/users', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['associated_establishment']);
    }
}

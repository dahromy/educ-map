<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the categories index endpoint.
     *
     * @return void
     */
    public function test_index_endpoint()
    {
        // Create categories
        Category::factory()->count(5)->create();

        // Make request to index endpoint
        $response = $this->get('/api/categories');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    /**
     * Test the category show endpoint.
     *
     * @return void
     */
    public function test_show_endpoint()
    {
        // Create a category
        $category = Category::factory()->create();

        // Make request to show endpoint
        $response = $this->get('/api/categories/' . $category->id);

        // Assert response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $category->id,
                    'name' => $category->category_name,
                ]
            ]);
    }

    /**
     * Test the category show endpoint with establishments count.
     *
     * @return void
     */
    public function test_show_endpoint_with_establishments_count()
    {
        // Create a category
        $category = Category::factory()->create();

        // Make request to show endpoint with include_establishments parameter
        $response = $this->get('/api/categories/' . $category->id . '?include_establishments=true');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'establishments_count',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /**
     * Test creating a category (admin only).
     *
     * @return void
     */
    public function test_store_endpoint_as_admin()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'roles' => ['ROLE_ADMIN']
        ]);

        // Authenticate as admin
        $this->actingAs($admin, 'sanctum');

        // Make request to store endpoint
        $response = $this->postJson('/api/categories', [
            'category_name' => 'Test Category',
            'description' => 'Test description'
        ]);

        // Assert response
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ]);

        // Assert database
        $this->assertDatabaseHas('categories', [
            'category_name' => 'Test Category',
            'description' => 'Test description'
        ]);
    }

    /**
     * Test creating a category without admin role.
     *
     * @return void
     */
    public function test_store_endpoint_as_non_admin()
    {
        // Create a regular user
        $user = User::factory()->create([
            'roles' => ['ROLE_USER']
        ]);

        // Authenticate as regular user
        $this->actingAs($user, 'sanctum');

        // Make request to store endpoint
        $response = $this->postJson('/api/categories', [
            'category_name' => 'Test Category',
            'description' => 'Test description'
        ]);

        // Assert response (should be forbidden)
        $response->assertStatus(403);
    }

    /**
     * Test updating a category (admin only).
     *
     * @return void
     */
    public function test_update_endpoint_as_admin()
    {
        // Create an admin user and a category
        $admin = User::factory()->create([
            'roles' => ['ROLE_ADMIN']
        ]);
        $category = Category::factory()->create();

        // Authenticate as admin
        $this->actingAs($admin, 'sanctum');

        // Make request to update endpoint
        $response = $this->putJson('/api/categories/' . $category->id, [
            'category_name' => 'Updated Category',
            'description' => 'Updated description'
        ]);

        // Assert response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $category->id,
                    'name' => 'Updated Category',
                    'description' => 'Updated description'
                ]
            ]);

        // Assert database
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'category_name' => 'Updated Category',
            'description' => 'Updated description'
        ]);
    }

    /**
     * Test deleting a category (admin only).
     *
     * @return void
     */
    public function test_delete_endpoint_as_admin()
    {
        // Create an admin user and a category
        $admin = User::factory()->create([
            'roles' => ['ROLE_ADMIN']
        ]);
        $category = Category::factory()->create();

        // Authenticate as admin
        $this->actingAs($admin, 'sanctum');

        // Make request to delete endpoint
        $response = $this->delete('/api/categories/' . $category->id);

        // Assert response
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category deleted successfully'
            ]);

        // Assert database
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }

    /**
     * Test validation errors when creating a category.
     *
     * @return void
     */
    public function test_store_validation_errors()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'roles' => ['ROLE_ADMIN']
        ]);

        // Authenticate as admin
        $this->actingAs($admin, 'sanctum');

        // Make request with missing required field
        $response = $this->postJson('/api/categories', [
            'description' => 'Test description'
            // Missing category_name
        ]);

        // Assert validation error
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_name']);
    }

    /**
     * Test filtering categories by search term.
     *
     * @return void
     */
    public function test_index_with_search_filter()
    {
        // Create categories
        Category::factory()->create(['category_name' => 'Public University']);
        Category::factory()->create(['category_name' => 'Private University']);
        Category::factory()->create(['category_name' => 'Technical Institute']);

        // Make request with search filter
        $response = $this->get('/api/categories?search=University');

        // Assert response contains only universities
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}

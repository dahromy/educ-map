<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Establishment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstablishmentApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the establishments index endpoint.
     *
     * @return void
     */
    public function test_index_endpoint()
    {
        // Create a category
        $category = Category::factory()->create();

        // Create establishments
        Establishment::factory()->count(5)->create([
            'category_id' => $category->id
        ]);

        // Make request to index endpoint
        $response = $this->get('/api/establishments');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'abbreviation',
                        'location',
                        'logo_url'
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    /**
     * Test the establishment show endpoint.
     *
     * @return void
     */
    public function test_show_endpoint()
    {
        // Create a category
        $category = Category::factory()->create();

        // Create an establishment
        $establishment = Establishment::factory()->create([
            'category_id' => $category->id
        ]);

        // Make request to show endpoint
        $response = $this->get('/api/establishments/' . $establishment->id);

        // Assert response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $establishment->id,
                    'name' => $establishment->name,
                ]
            ]);
    }

    /**
     * Test the recent establishments endpoint.
     *
     * @return void
     */
    public function test_recent_endpoint()
    {
        // Create a category
        $category = Category::factory()->create();

        // Create establishments
        Establishment::factory()->count(3)->create([
            'category_id' => $category->id,
            'created_at' => now()->subDays(10) // Recent establishments
        ]);

        Establishment::factory()->count(2)->create([
            'category_id' => $category->id,
            'created_at' => now()->subDays(60) // Older establishments
        ]);

        // Make request to recent endpoint
        $response = $this->get('/api/establishments/recent');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * Test the map markers endpoint.
     *
     * @return void
     */
    public function test_map_markers_endpoint()
    {
        // Create a category
        $category = Category::factory()->create();

        // Create establishments with coordinates
        Establishment::factory()->count(4)->create([
            'category_id' => $category->id,
            'latitude' => 12.3456,
            'longitude' => 45.6789
        ]);

        // Make request to map markers endpoint
        $response = $this->get('/api/map/markers');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(4, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'latitude',
                        'longitude'
                    ]
                ]
            ]);
    }

    /**
     * Test the compare establishments endpoint.
     *
     * @return void
     */
    public function test_compare_endpoint()
    {
        // Create a category
        $category = Category::factory()->create();

        // Create establishments
        $establishment1 = Establishment::factory()->create([
            'category_id' => $category->id
        ]);

        $establishment2 = Establishment::factory()->create([
            'category_id' => $category->id
        ]);

        // Make request to compare endpoint
        $response = $this->get('/api/compare?ids[]=' . $establishment1->id . '&ids[]=' . $establishment2->id);

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'indicators'
                    ]
                ]
            ]);
    }

    /**
     * Test that an admin user can create an establishment.
     *
     * @return void
     */
    public function test_store_endpoint_with_admin_permissions()
    {
        // Create admin user
        $admin = User::factory()->create([
            'roles' => ['ROLE_ADMIN']
        ]);

        // Create a category
        $category = Category::factory()->create();

        // Prepare establishment data
        $establishmentData = [
            'name' => 'New University',
            'abbreviation' => 'NU',
            'category_id' => $category->id,
            'region' => 'North',
        ];

        // Make request to store endpoint as admin
        $response = $this->actingAs($admin)
            ->postJson('/api/establishments', $establishmentData);

        // Assert response
        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'New University');

        // Assert database has the record
        $this->assertDatabaseHas('establishments', [
            'name' => 'New University',
            'abbreviation' => 'NU',
        ]);
    }

    /**
     * Test that a regular user cannot create an establishment.
     *
     * @return void
     */
    public function test_store_endpoint_without_admin_permissions()
    {
        // Create regular user
        $user = User::factory()->create([
            'roles' => ['ROLE_USER']
        ]);

        // Create a category
        $category = Category::factory()->create();

        // Prepare establishment data
        $establishmentData = [
            'name' => 'New University',
            'abbreviation' => 'NU',
            'category_id' => $category->id,
        ];

        // Make request to store endpoint as regular user
        $response = $this->actingAs($user)
            ->postJson('/api/establishments', $establishmentData);

        // Assert response
        $response->assertStatus(403);
    }
}

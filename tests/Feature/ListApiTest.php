<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Domain;
use App\Models\Grade;
use App\Models\Label;
use App\Models\Mention;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the domains endpoint.
     *
     * @return void
     */
    public function test_domains_endpoint()
    {
        // Create domains
        Domain::factory()->count(3)->create();

        // Make request to domains endpoint
        $response = $this->get('/api/domains');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description'
                    ]
                ]
            ]);
    }

    /**
     * Test the grades endpoint.
     *
     * @return void
     */
    public function test_grades_endpoint()
    {
        // Create grades
        Grade::factory()->count(3)->create();

        // Make request to grades endpoint
        $response = $this->get('/api/grades');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'level',
                        'description'
                    ]
                ]
            ]);
    }

    /**
     * Test the mentions endpoint.
     *
     * @return void
     */
    public function test_mentions_endpoint()
    {
        // Create mentions
        Mention::factory()->count(3)->create();

        // Make request to mentions endpoint
        $response = $this->get('/api/mentions');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description'
                    ]
                ]
            ]);
    }

    /**
     * Test the labels endpoint.
     *
     * @return void
     */
    public function test_labels_endpoint()
    {
        // Create labels
        Label::factory()->count(3)->create();

        // Make request to labels endpoint
        $response = $this->get('/api/labels');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description'
                    ]
                ]
            ]);
    }

    /**
     * Test that all list endpoints are properly ordered.
     *
     * @return void
     */
    public function test_endpoints_ordering()
    {
        // Create domains with specific names to test ordering
        Domain::create(['name' => 'Zebra Domain', 'description' => 'Z domain']);
        Domain::create(['name' => 'Alpha Domain', 'description' => 'A domain']);
        Domain::create(['name' => 'Beta Domain', 'description' => 'B domain']);

        // Make request to domains endpoint
        $response = $this->get('/api/domains');

        // Assert response is ordered by name
        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals('Alpha Domain', $data[0]['name']);
        $this->assertEquals('Beta Domain', $data[1]['name']);
        $this->assertEquals('Zebra Domain', $data[2]['name']);
    }

    /**
     * Test that grades are ordered by level then name.
     *
     * @return void
     */
    public function test_grades_ordering()
    {
        // Create grades with specific levels to test ordering
        Grade::create(['name' => 'Master', 'level' => 2, 'description' => 'Masters degree']);
        Grade::create(['name' => 'Licence', 'level' => 1, 'description' => 'Bachelors degree']);
        Grade::create(['name' => 'Doctorat', 'level' => 3, 'description' => 'Doctoral degree']);

        // Make request to grades endpoint
        $response = $this->get('/api/grades');

        // Assert response is ordered by level
        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals(1, $data[0]['level']);
        $this->assertEquals(2, $data[1]['level']);
        $this->assertEquals(3, $data[2]['level']);
    }
}

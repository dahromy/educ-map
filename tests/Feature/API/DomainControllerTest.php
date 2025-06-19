<?php

namespace Tests\Feature\API;

use App\Models\Category;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['roles' => ['ROLE_ADMIN']]);
        $this->user = User::factory()->create(['roles' => ['ROLE_USER']]);
    }

    public function test_can_list_domains()
    {
        Domain::factory()->count(3)->create();

        $response = $this->getJson('/api/domains');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'created_at', 'updated_at']
                ]
            ]);
    }

    public function test_can_search_domains()
    {
        Domain::factory()->create(['name' => 'Informatique']);
        Domain::factory()->create(['name' => 'Mathématiques']);

        $response = $this->getJson('/api/domains?search=Info');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Informatique']);
    }

    public function test_can_sort_domains()
    {
        Domain::factory()->create(['name' => 'Z Domain']);
        Domain::factory()->create(['name' => 'A Domain']);

        $response = $this->getJson('/api/domains?sort_by=name&sort_direction=asc');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('A Domain', $data[0]['name']);
        $this->assertEquals('Z Domain', $data[1]['name']);
    }

    public function test_can_show_domain()
    {
        $domain = Domain::factory()->create();

        $response = $this->getJson("/api/domains/{$domain->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'description', 'created_at', 'updated_at']
            ])
            ->assertJsonFragment(['id' => $domain->id]);
    }

    public function test_admin_can_create_domain()
    {
        $domainData = [
            'name' => 'Sciences Physiques',
            'description' => 'Domaine des sciences physiques'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/domains', $domainData);

        $response->assertStatus(201)
            ->assertJsonFragment($domainData);

        $this->assertDatabaseHas('domains', $domainData);
    }

    public function test_user_cannot_create_domain()
    {
        $domainData = [
            'name' => 'Sciences Physiques',
            'description' => 'Domaine des sciences physiques'
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/domains', $domainData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('domains', $domainData);
    }

    public function test_guest_cannot_create_domain()
    {
        $domainData = [
            'name' => 'Sciences Physiques',
            'description' => 'Domaine des sciences physiques'
        ];

        $response = $this->postJson('/api/domains', $domainData);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('domains', $domainData);
    }

    public function test_admin_can_update_domain()
    {
        $domain = Domain::factory()->create();
        $updateData = [
            'name' => 'Updated Domain Name',
            'description' => 'Updated description'
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/domains/{$domain->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('domains', array_merge(['id' => $domain->id], $updateData));
    }

    public function test_user_cannot_update_domain()
    {
        $domain = Domain::factory()->create();
        $updateData = [
            'name' => 'Updated Domain Name',
            'description' => 'Updated description'
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/domains/{$domain->id}", $updateData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('domains', $updateData);
    }

    public function test_admin_can_delete_domain()
    {
        $domain = Domain::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/domains/{$domain->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Domain deleted successfully']);

        $this->assertDatabaseMissing('domains', ['id' => $domain->id]);
    }

    public function test_cannot_delete_domain_with_program_offerings()
    {
        $domain = Domain::factory()->create();
        // Create a program offering linked to this domain
        \App\Models\ProgramOffering::factory()->create(['domain_id' => $domain->id]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/domains/{$domain->id}");

        $response->assertStatus(409)
            ->assertJsonFragment(['message' => 'Cannot delete domain. It has associated program offerings.']);

        $this->assertDatabaseHas('domains', ['id' => $domain->id]);
    }

    public function test_domain_name_must_be_unique()
    {
        Domain::factory()->create(['name' => 'Existing Domain']);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/domains', [
                'name' => 'Existing Domain',
                'description' => 'Test description'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_domain_name_is_required()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/domains', [
                'description' => 'Test description'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}

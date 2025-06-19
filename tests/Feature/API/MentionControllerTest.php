<?php

namespace Tests\Feature\API;

use App\Models\Mention;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentionControllerTest extends TestCase
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

    public function test_can_list_mentions()
    {
        Mention::factory()->count(3)->create();

        $response = $this->getJson('/api/mentions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'created_at', 'updated_at']
                ]
            ]);
    }

    public function test_can_search_mentions()
    {
        Mention::factory()->create(['name' => 'Génie Logiciel']);
        Mention::factory()->create(['name' => 'Intelligence Artificielle']);

        $response = $this->getJson('/api/mentions?search=Génie');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Génie Logiciel']);
    }

    public function test_can_show_mention()
    {
        $mention = Mention::factory()->create();

        $response = $this->getJson("/api/mentions/{$mention->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'description', 'created_at', 'updated_at']
            ])
            ->assertJsonFragment(['id' => $mention->id]);
    }

    public function test_admin_can_create_mention()
    {
        $mentionData = [
            'name' => 'Cybersécurité',
            'description' => 'Spécialisation en cybersécurité'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/mentions', $mentionData);

        $response->assertStatus(201)
            ->assertJsonFragment($mentionData);

        $this->assertDatabaseHas('mentions', $mentionData);
    }

    public function test_user_cannot_create_mention()
    {
        $mentionData = [
            'name' => 'Cybersécurité',
            'description' => 'Spécialisation en cybersécurité'
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/mentions', $mentionData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('mentions', $mentionData);
    }

    public function test_admin_can_update_mention()
    {
        $mention = Mention::factory()->create();
        $updateData = [
            'name' => 'Updated Mention Name',
            'description' => 'Updated description'
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/mentions/{$mention->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('mentions', array_merge(['id' => $mention->id], $updateData));
    }

    public function test_admin_can_delete_mention()
    {
        $mention = Mention::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/mentions/{$mention->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Mention deleted successfully']);

        $this->assertDatabaseMissing('mentions', ['id' => $mention->id]);
    }

    public function test_cannot_delete_mention_with_program_offerings()
    {
        $mention = Mention::factory()->create();
        // Create a program offering linked to this mention
        \App\Models\ProgramOffering::factory()->create(['mention_id' => $mention->id]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/mentions/{$mention->id}");

        $response->assertStatus(409)
            ->assertJsonFragment(['message' => 'Cannot delete mention. It has associated program offerings.']);

        $this->assertDatabaseHas('mentions', ['id' => $mention->id]);
    }

    public function test_mention_name_must_be_unique()
    {
        Mention::factory()->create(['name' => 'Existing Mention']);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/mentions', [
                'name' => 'Existing Mention',
                'description' => 'Test description'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_mention_name_is_required()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/mentions', [
                'description' => 'Test description'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}

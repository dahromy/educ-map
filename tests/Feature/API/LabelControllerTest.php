<?php

namespace Tests\Feature\API;

use App\Models\Label;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabelControllerTest extends TestCase
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

    public function test_can_list_labels()
    {
        Label::factory()->count(3)->create();

        $response = $this->getJson('/api/labels');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'color', 'description', 'created_at', 'updated_at']
                ]
            ]);
    }

    public function test_can_search_labels()
    {
        Label::factory()->create(['name' => 'Excellence']);
        Label::factory()->create(['name' => 'Innovation']);

        $response = $this->getJson('/api/labels?search=Excel');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Excellence']);
    }

    public function test_can_show_label()
    {
        $label = Label::factory()->create();

        $response = $this->getJson("/api/labels/{$label->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'color', 'description', 'created_at', 'updated_at']
            ])
            ->assertJsonFragment(['id' => $label->id]);
    }

    public function test_admin_can_create_label()
    {
        $labelData = [
            'name' => 'Qualité',
            'color' => '#FF5733',
            'description' => 'Label de qualité'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/labels', $labelData);

        $response->assertStatus(201)
            ->assertJsonFragment($labelData);

        $this->assertDatabaseHas('labels', $labelData);
    }

    public function test_user_cannot_create_label()
    {
        $labelData = [
            'name' => 'Qualité',
            'color' => '#FF5733',
            'description' => 'Label de qualité'
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/labels', $labelData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('labels', $labelData);
    }

    public function test_admin_can_update_label()
    {
        $label = Label::factory()->create();
        $updateData = [
            'name' => 'Updated Label Name',
            'color' => '#33FF57',
            'description' => 'Updated description'
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/labels/{$label->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('labels', array_merge(['id' => $label->id], $updateData));
    }

    public function test_admin_can_delete_label()
    {
        $label = Label::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/labels/{$label->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Label deleted successfully']);

        $this->assertDatabaseMissing('labels', ['id' => $label->id]);
    }

    public function test_cannot_delete_label_with_establishments()
    {
        $label = Label::factory()->create();
        $establishment = \App\Models\Establishment::factory()->create();

        // Create a pivot relationship
        \App\Models\EstablishmentLabel::factory()->create([
            'establishment_id' => $establishment->id,
            'label_id' => $label->id
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/labels/{$label->id}");

        $response->assertStatus(409)
            ->assertJsonFragment(['message' => 'Cannot delete label. It has associated establishments.']);

        $this->assertDatabaseHas('labels', ['id' => $label->id]);
    }

    public function test_label_name_must_be_unique()
    {
        Label::factory()->create(['name' => 'Existing Label']);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/labels', [
                'name' => 'Existing Label',
                'description' => 'Test description'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_label_color_validation()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/labels', [
                'name' => 'Test Label',
                'color' => 'invalid-color'  // Invalid color format
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['color']);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/labels', [
                'name' => 'Test Label 2',
                'color' => '#FF57'  // Invalid: too short
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['color']);
    }

    public function test_valid_label_color_format()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/labels', [
                'name' => 'Test Label',
                'color' => '#FF5733'  // Valid hex color
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('labels', ['name' => 'Test Label', 'color' => '#FF5733']);
    }

    public function test_label_name_is_required()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/labels', [
                'color' => '#FF5733'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}

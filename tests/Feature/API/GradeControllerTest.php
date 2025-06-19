<?php

namespace Tests\Feature\API;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeControllerTest extends TestCase
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

    public function test_can_list_grades()
    {
        Grade::factory()->count(3)->create();

        $response = $this->getJson('/api/grades');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'level', 'description', 'created_at', 'updated_at']
                ]
            ]);
    }

    public function test_can_search_grades()
    {
        Grade::factory()->create(['name' => 'Licence']);
        Grade::factory()->create(['name' => 'Master']);

        $response = $this->getJson('/api/grades?search=Lic');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Licence']);
    }

    public function test_can_sort_grades_by_level()
    {
        Grade::factory()->create(['name' => 'Master', 'level' => 5]);
        Grade::factory()->create(['name' => 'Licence', 'level' => 3]);

        $response = $this->getJson('/api/grades?sort_by=level&sort_direction=asc');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('Licence', $data[0]['name']);
        $this->assertEquals('Master', $data[1]['name']);
    }

    public function test_can_show_grade()
    {
        $grade = Grade::factory()->create();

        $response = $this->getJson("/api/grades/{$grade->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'level', 'description', 'created_at', 'updated_at']
            ])
            ->assertJsonFragment(['id' => $grade->id]);
    }

    public function test_admin_can_create_grade()
    {
        $gradeData = [
            'name' => 'Doctorat',
            'level' => 8,
            'description' => 'Niveau doctorat'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/grades', $gradeData);

        $response->assertStatus(201)
            ->assertJsonFragment($gradeData);

        $this->assertDatabaseHas('grades', $gradeData);
    }

    public function test_user_cannot_create_grade()
    {
        $gradeData = [
            'name' => 'Doctorat',
            'level' => 8,
            'description' => 'Niveau doctorat'
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/grades', $gradeData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('grades', $gradeData);
    }

    public function test_admin_can_update_grade()
    {
        $grade = Grade::factory()->create();
        $updateData = [
            'name' => 'Updated Grade Name',
            'level' => 6,
            'description' => 'Updated description'
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/grades/{$grade->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('grades', array_merge(['id' => $grade->id], $updateData));
    }

    public function test_admin_can_delete_grade()
    {
        $grade = Grade::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/grades/{$grade->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Grade deleted successfully']);

        $this->assertDatabaseMissing('grades', ['id' => $grade->id]);
    }

    public function test_cannot_delete_grade_with_program_offerings()
    {
        $grade = Grade::factory()->create();
        // Create a program offering linked to this grade
        \App\Models\ProgramOffering::factory()->create(['grade_id' => $grade->id]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/grades/{$grade->id}");

        $response->assertStatus(409)
            ->assertJsonFragment(['message' => 'Cannot delete grade. It has associated program offerings.']);

        $this->assertDatabaseHas('grades', ['id' => $grade->id]);
    }

    public function test_grade_name_must_be_unique()
    {
        Grade::factory()->create(['name' => 'Existing Grade']);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/grades', [
                'name' => 'Existing Grade',
                'level' => 3
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_grade_level_validation()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/grades', [
                'name' => 'Test Grade',
                'level' => 15  // Invalid: max is 10
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['level']);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/grades', [
                'name' => 'Test Grade 2',
                'level' => 0  // Invalid: min is 1
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['level']);
    }

    public function test_grade_name_is_required()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/grades', [
                'level' => 3
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}

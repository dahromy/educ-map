<?php

namespace Tests\Feature\API;

use App\Models\Category;
use App\Models\Domain;
use App\Models\Establishment;
use App\Models\Grade;
use App\Models\Mention;
use App\Models\ProgramOffering;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EstablishmentDirectProgramUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $managerUser;
    private Establishment $establishment;
    private Category $category;
    private Domain $domain;
    private Grade $grade;
    private Mention $mention;

    protected function setUp(): void
    {
        parent::setUp();

        // Create basic data
        $this->category = Category::factory()->create();
        $this->domain = Domain::factory()->create();
        $this->grade = Grade::factory()->create();
        $this->mention = Mention::factory()->create();

        // Create establishment with manager
        $this->establishment = Establishment::factory()->create([
            'category_id' => $this->category->id,
        ]);

        $this->managerUser = User::factory()->create([
            'roles' => ['ROLE_ESTABLISHMENT'],
            'associated_establishment' => $this->establishment->id,
        ]);
    }

    public function test_manager_can_add_direct_program_offerings()
    {
        Sanctum::actingAs($this->managerUser);

        $payload = [
            'name' => 'Updated Establishment',
            'program_offerings' => [
                [
                    'domain_id' => $this->domain->id,
                    'grade_id' => $this->grade->id,
                    'mention_id' => $this->mention->id,
                    'tuition_fees_info' => '500,000 Ar/year',
                    'program_duration_info' => '3 years',
                ],
            ],
        ];

        $response = $this->putJson("/api/establishments/{$this->establishment->id}", $payload);

        $response->assertStatus(200);

        // Verify program was created without department
        $this->assertDatabaseHas('program_offerings', [
            'establishment_id' => $this->establishment->id,
            'department_id' => null,
            'domain_id' => $this->domain->id,
            'grade_id' => $this->grade->id,
            'mention_id' => $this->mention->id,
            'tuition_fees_info' => '500,000 Ar/year',
            'program_duration_info' => '3 years',
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'program_offerings' => [
                    '*' => [
                        'id',
                        'domain',
                        'grade',
                        'mention',
                        'tuition_fees_info',
                        'program_duration_info',
                        'department',
                    ],
                ],
            ],
        ]);
    }

    public function test_manager_can_update_direct_program_offerings()
    {
        Sanctum::actingAs($this->managerUser);

        // Create existing direct program
        $program = ProgramOffering::factory()->create([
            'establishment_id' => $this->establishment->id,
            'department_id' => null, // Direct program (no department)
            'domain_id' => $this->domain->id,
            'grade_id' => $this->grade->id,
            'mention_id' => $this->mention->id,
            'tuition_fees_info' => '400,000 Ar/year',
        ]);

        $payload = [
            'name' => 'Updated Establishment',
            'program_offerings' => [
                [
                    'id' => $program->id,
                    'domain_id' => $this->domain->id,
                    'grade_id' => $this->grade->id,
                    'mention_id' => $this->mention->id,
                    'tuition_fees_info' => '600,000 Ar/year', // Updated
                    'program_duration_info' => '4 years', // Updated
                ],
            ],
        ];

        $response = $this->putJson("/api/establishments/{$this->establishment->id}", $payload);

        $response->assertStatus(200);

        // Verify program was updated
        $this->assertDatabaseHas('program_offerings', [
            'id' => $program->id,
            'establishment_id' => $this->establishment->id,
            'department_id' => null,
            'tuition_fees_info' => '600,000 Ar/year',
            'program_duration_info' => '4 years',
        ]);
    }

    public function test_manager_can_delete_direct_program_offerings()
    {
        Sanctum::actingAs($this->managerUser);

        // Create existing direct programs
        $program1 = ProgramOffering::factory()->create([
            'establishment_id' => $this->establishment->id,
            'department_id' => null,
        ]);

        $program2 = ProgramOffering::factory()->create([
            'establishment_id' => $this->establishment->id,
            'department_id' => null,
        ]);

        // Only include program1 in update (program2 should be deleted)
        $payload = [
            'name' => 'Updated Establishment',
            'program_offerings' => [
                [
                    'id' => $program1->id,
                    'domain_id' => $this->domain->id,
                    'grade_id' => $this->grade->id,
                    'mention_id' => $this->mention->id,
                    'tuition_fees_info' => '500,000 Ar/year',
                ],
            ],
        ];

        $response = $this->putJson("/api/establishments/{$this->establishment->id}", $payload);

        $response->assertStatus(200);

        // Verify program1 still exists
        $this->assertDatabaseHas('program_offerings', [
            'id' => $program1->id,
        ]);

        // Verify program2 was deleted
        $this->assertDatabaseMissing('program_offerings', [
            'id' => $program2->id,
        ]);
    }

    public function test_manager_can_manage_both_nested_and_direct_programs()
    {
        Sanctum::actingAs($this->managerUser);

        $domain2 = Domain::factory()->create();
        $grade2 = Grade::factory()->create();
        $mention2 = Mention::factory()->create();

        $payload = [
            'name' => 'Updated Establishment',
            'departments' => [
                [
                    'name' => 'Department 1',
                    'abbreviation' => 'DEPT1',
                    'program_offerings' => [
                        [
                            'domain_id' => $this->domain->id,
                            'grade_id' => $this->grade->id,
                            'mention_id' => $this->mention->id,
                            'tuition_fees_info' => '400,000 Ar/year',
                        ],
                    ],
                ],
            ],
            'program_offerings' => [
                [
                    'domain_id' => $domain2->id,
                    'grade_id' => $grade2->id,
                    'mention_id' => $mention2->id,
                    'tuition_fees_info' => '500,000 Ar/year',
                ],
            ],
        ];

        $response = $this->putJson("/api/establishments/{$this->establishment->id}", $payload);

        $response->assertStatus(200);

        // Verify nested program was created with department
        $this->assertDatabaseHas('program_offerings', [
            'establishment_id' => $this->establishment->id,
            'domain_id' => $this->domain->id,
            'grade_id' => $this->grade->id,
            'mention_id' => $this->mention->id,
            'tuition_fees_info' => '400,000 Ar/year',
        ]);

        // Verify direct program was created without department
        $this->assertDatabaseHas('program_offerings', [
            'establishment_id' => $this->establishment->id,
            'department_id' => null,
            'domain_id' => $domain2->id,
            'grade_id' => $grade2->id,
            'mention_id' => $mention2->id,
            'tuition_fees_info' => '500,000 Ar/year',
        ]);

        // Verify we have one department
        $this->assertDatabaseHas('departments', [
            'establishment_id' => $this->establishment->id,
            'name' => 'Department 1',
        ]);
    }

    public function test_validation_fails_for_missing_required_fields_in_direct_programs()
    {
        Sanctum::actingAs($this->managerUser);

        $payload = [
            'name' => 'Updated Establishment',
            'program_offerings' => [
                [
                    // Missing required fields
                    'tuition_fees_info' => '500,000 Ar/year',
                ],
            ],
        ];

        $response = $this->putJson("/api/establishments/{$this->establishment->id}", $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'program_offerings.0.domain_id',
            'program_offerings.0.grade_id',
            'program_offerings.0.mention_id',
        ]);
    }

    public function test_unauthorized_user_cannot_update_direct_programs()
    {
        $otherUser = User::factory()->create(['roles' => ['ROLE_USER']]);
        Sanctum::actingAs($otherUser);

        $payload = [
            'name' => 'Updated Establishment',
            'program_offerings' => [
                [
                    'domain_id' => $this->domain->id,
                    'grade_id' => $this->grade->id,
                    'mention_id' => $this->mention->id,
                    'tuition_fees_info' => '500,000 Ar/year',
                ],
            ],
        ];

        $response = $this->putJson("/api/establishments/{$this->establishment->id}", $payload);

        $response->assertStatus(403);
    }
}

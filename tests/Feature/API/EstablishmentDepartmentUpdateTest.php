<?php

namespace Tests\Feature\API;

use App\Models\Establishment;
use App\Models\Department;
use App\Models\ProgramOffering;
use App\Models\Category;
use App\Models\Domain;
use App\Models\Grade;
use App\Models\Mention;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstablishmentDepartmentUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $establishmentUser;
    private Establishment $establishment;
    private Category $category;
    private Domain $domain;
    private Grade $grade;
    private Mention $mention;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->category = Category::factory()->create();
        $this->domain = Domain::factory()->create();
        $this->grade = Grade::factory()->create();
        $this->mention = Mention::factory()->create();

        // Create admin user
        $this->adminUser = User::factory()->create([
            'roles' => ['ROLE_ADMIN']
        ]);

        // Create establishment
        $this->establishment = Establishment::factory()->create([
            'category_id' => $this->category->id
        ]);

        // Create establishment user
        $this->establishmentUser = User::factory()->create([
            'roles' => ['ROLE_ESTABLISHMENT'],
            'associated_establishment' => $this->establishment->id
        ]);
    }

    public function test_establishment_user_can_add_new_department()
    {
        $response = $this->actingAs($this->establishmentUser)
            ->putJson("/api/establishments/{$this->establishment->id}", [
                'name' => $this->establishment->name,
                'departments' => [
                    [
                        'name' => 'Computer Science Department',
                        'abbreviation' => 'CS',
                        'description' => 'Department of Computer Science'
                    ]
                ]
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('departments', [
            'establishment_id' => $this->establishment->id,
            'name' => 'Computer Science Department',
            'abbreviation' => 'CS'
        ]);
    }

    public function test_establishment_user_can_add_department_with_programs()
    {
        $response = $this->actingAs($this->establishmentUser)
            ->putJson("/api/establishments/{$this->establishment->id}", [
                'name' => $this->establishment->name,
                'departments' => [
                    [
                        'name' => 'Engineering Department',
                        'abbreviation' => 'ENG',
                        'program_offerings' => [
                            [
                                'domain_id' => $this->domain->id,
                                'grade_id' => $this->grade->id,
                                'mention_id' => $this->mention->id,
                                'tuition_fees_info' => '1000 USD/year',
                                'program_duration_info' => '4 years'
                            ]
                        ]
                    ]
                ]
            ]);

        $response->assertOk();

        $department = Department::where('name', 'Engineering Department')->first();
        $this->assertNotNull($department);

        $this->assertDatabaseHas('program_offerings', [
            'establishment_id' => $this->establishment->id,
            'department_id' => $department->id,
            'domain_id' => $this->domain->id,
            'grade_id' => $this->grade->id,
            'mention_id' => $this->mention->id
        ]);
    }

    public function test_establishment_user_can_update_existing_department()
    {
        // Create existing department
        $department = Department::factory()->create([
            'establishment_id' => $this->establishment->id,
            'name' => 'Old Department Name'
        ]);

        $response = $this->actingAs($this->establishmentUser)
            ->putJson("/api/establishments/{$this->establishment->id}", [
                'name' => $this->establishment->name,
                'departments' => [
                    [
                        'id' => $department->id,
                        'name' => 'Updated Department Name',
                        'abbreviation' => 'UDN'
                    ]
                ]
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'Updated Department Name',
            'abbreviation' => 'UDN'
        ]);
    }

    public function test_establishment_user_cannot_modify_other_establishment_department()
    {
        // Create another establishment and department
        $otherEstablishment = Establishment::factory()->create([
            'category_id' => $this->category->id
        ]);
        $otherDepartment = Department::factory()->create([
            'establishment_id' => $otherEstablishment->id
        ]);

        $response = $this->actingAs($this->establishmentUser)
            ->putJson("/api/establishments/{$this->establishment->id}", [
                'name' => $this->establishment->name,
                'departments' => [
                    [
                        'id' => $otherDepartment->id,
                        'name' => 'Trying to update other department'
                    ]
                ]
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['departments.0.id']);
    }

    public function test_admin_user_can_manage_any_establishment_departments()
    {
        $response = $this->actingAs($this->adminUser)
            ->putJson("/api/establishments/{$this->establishment->id}", [
                'name' => $this->establishment->name,
                'departments' => [
                    [
                        'name' => 'Admin Added Department',
                        'abbreviation' => 'AAD'
                    ]
                ]
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('departments', [
            'establishment_id' => $this->establishment->id,
            'name' => 'Admin Added Department'
        ]);
    }

    public function test_response_includes_departments_and_programs()
    {
        $response = $this->actingAs($this->establishmentUser)
            ->putJson("/api/establishments/{$this->establishment->id}", [
                'name' => $this->establishment->name,
                'departments' => [
                    [
                        'name' => 'Test Department',
                        'program_offerings' => [
                            [
                                'domain_id' => $this->domain->id,
                                'grade_id' => $this->grade->id,
                                'mention_id' => $this->mention->id
                            ]
                        ]
                    ]
                ]
            ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'departments' => [
                    '*' => ['id', 'name', 'abbreviation', 'description']
                ],
                'program_offerings' => [
                    '*' => [
                        'id',
                        'department',
                        'domain',
                        'grade',
                        'mention'
                    ]
                ]
            ]
        ]);
    }
}

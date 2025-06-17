<?php

namespace Tests\Feature\API;

use App\Models\Category;
use App\Models\Establishment;
use App\Models\Label;
use App\Models\User;
use App\Models\ProgramOffering;
use App\Models\Department;
use App\Models\Domain;
use App\Models\Grade;
use App\Models\Mention;
use App\Models\Accreditation;
use App\Models\Reference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EstablishmentListEnhancedTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $category;
    protected $label;
    protected $domain;
    protected $grade;
    protected $mention;
    protected $reference;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->category = Category::factory()->create(['category_name' => 'Public University']);
        $this->label = Label::factory()->create(['name' => 'Excellence']);
        $this->domain = Domain::factory()->create(['name' => 'Computer Science']);
        $this->grade = Grade::factory()->create(['name' => 'Master']);
        $this->mention = Mention::factory()->create(['name' => 'Software Engineering']);
        $this->reference = Reference::factory()->create();
    }

    public function test_establishment_list_includes_enhanced_details()
    {
        // Create establishment with all relationships
        $establishment = Establishment::factory()->create([
            'name' => 'Test University',
            'abbreviation' => 'TU',
            'description' => 'A test university',
            'category_id' => $this->category->id,
            'student_count' => 2000,
            'success_rate' => 85.5,
            'professional_insertion_rate' => 78.2,
            'first_habilitation_year' => 1995,
            'phone' => '+261 20 22 123 45',
            'email' => 'contact@test.mg',
            'website' => 'https://test.mg',
        ]);

        // Attach label
        $establishment->labels()->attach($this->label);

        // Create department
        $department = Department::factory()->create([
            'establishment_id' => $establishment->id
        ]);

        // Create program offering
        $programOffering = ProgramOffering::factory()->create([
            'establishment_id' => $establishment->id,
            'department_id' => $department->id,
            'domain_id' => $this->domain->id,
            'grade_id' => $this->grade->id,
            'mention_id' => $this->mention->id,
        ]);

        // Create recent accreditation
        Accreditation::factory()->create([
            'program_offering_id' => $programOffering->id,
            'reference_id' => $this->reference->id,
            'is_recent' => true,
            'accreditation_date' => now()->subMonths(2),
        ]);

        // Make API request
        $response = $this->getJson('/api/establishments');

        // Assert response structure and data
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'abbreviation',
                        'description',
                        'category' => ['id', 'name'],
                        'location' => ['address', 'region', 'city', 'latitude', 'longitude'],
                        'contact' => ['phone', 'email', 'website'],
                        'indicators' => [
                            'student_count',
                            'success_rate',
                            'professional_insertion_rate',
                            'first_habilitation_year'
                        ],
                        'labels' => [
                            '*' => ['id', 'name', 'description']
                        ],
                        'programs_summary' => [
                            'total_programs',
                            'domains_count',
                            'grades_offered',
                            'departments_count'
                        ],
                        'recent_accreditation' => [
                            'has_recent'
                        ],
                        'logo_url',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);

        // Assert specific data
        $data = $response->json('data.0');
        $this->assertEquals('Test University', $data['name']);
        $this->assertEquals('TU', $data['abbreviation']);
        $this->assertEquals('A test university', $data['description']);
        $this->assertEquals(2000, $data['indicators']['student_count']);
        $this->assertEquals(85.5, $data['indicators']['success_rate']);
        $this->assertEquals('Excellence', $data['labels'][0]['name']);
        $this->assertEquals(1, $data['programs_summary']['total_programs']);
        $this->assertEquals(1, $data['programs_summary']['domains_count']);
        $this->assertEquals(['Master'], $data['programs_summary']['grades_offered']);
        $this->assertEquals(1, $data['programs_summary']['departments_count']);
        $this->assertTrue($data['recent_accreditation']['has_recent']);
    }

    public function test_establishment_list_filtering_by_student_count()
    {
        // Create establishments with different student counts
        Establishment::factory()->create([
            'name' => 'Small University',
            'category_id' => $this->category->id,
            'student_count' => 500
        ]);

        Establishment::factory()->create([
            'name' => 'Large University',
            'category_id' => $this->category->id,
            'student_count' => 5000
        ]);

        // Test minimum student count filter
        $response = $this->getJson('/api/establishments?min_student_count=1000');
        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Large University', $data[0]['name']);

        // Test maximum student count filter
        $response = $this->getJson('/api/establishments?max_student_count=1000');
        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Small University', $data[0]['name']);
    }

    public function test_establishment_list_filtering_by_recent_accreditation()
    {
        // Create establishment with recent accreditation
        $establishment1 = Establishment::factory()->create([
            'name' => 'Recently Accredited University',
            'category_id' => $this->category->id,
        ]);

        $department1 = Department::factory()->create(['establishment_id' => $establishment1->id]);
        $programOffering1 = ProgramOffering::factory()->create([
            'establishment_id' => $establishment1->id,
            'department_id' => $department1->id,
            'domain_id' => $this->domain->id,
            'grade_id' => $this->grade->id,
            'mention_id' => $this->mention->id,
        ]);

        Accreditation::factory()->create([
            'program_offering_id' => $programOffering1->id,
            'reference_id' => $this->reference->id,
            'is_recent' => true,
        ]);

        // Create establishment without recent accreditation
        Establishment::factory()->create([
            'name' => 'Old University',
            'category_id' => $this->category->id,
        ]);

        // Test filtering by recent accreditation
        $response = $this->getJson('/api/establishments?has_recent_accreditation=true');
        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Recently Accredited University', $data[0]['name']);
    }

    public function test_establishment_list_sorting_by_indicators()
    {
        // Create establishments with different indicators
        Establishment::factory()->create([
            'name' => 'Low Success Rate University',
            'category_id' => $this->category->id,
            'success_rate' => 60.0
        ]);

        Establishment::factory()->create([
            'name' => 'High Success Rate University',
            'category_id' => $this->category->id,
            'success_rate' => 90.0
        ]);

        // Test sorting by success rate
        $response = $this->getJson('/api/establishments?sort_by=success_rate&sort_direction=desc');
        $response->assertOk();
        $data = $response->json('data');
        $this->assertEquals('High Success Rate University', $data[0]['name']);
        $this->assertEquals('Low Success Rate University', $data[1]['name']);
    }

    public function test_establishment_list_city_filtering()
    {
        Establishment::factory()->create([
            'name' => 'Antananarivo University',
            'category_id' => $this->category->id,
            'city' => 'Antananarivo'
        ]);

        Establishment::factory()->create([
            'name' => 'Toamasina University',
            'category_id' => $this->category->id,
            'city' => 'Toamasina'
        ]);

        // Test city filtering
        $response = $this->getJson('/api/establishments?city=Antananarivo');
        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Antananarivo University', $data[0]['name']);
    }
}

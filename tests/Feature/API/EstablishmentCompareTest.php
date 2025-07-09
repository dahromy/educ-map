<?php

namespace Tests\Feature\API;

use App\Models\Establishment;
use App\Models\Category;
use App\Models\Label;
use App\Models\Domain;
use App\Models\Grade;
use App\Models\Mention;
use App\Models\ProgramOffering;
use App\Models\Accreditation;
use App\Models\Reference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstablishmentCompareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_compare_establishments_with_basic_data()
    {
        // Create test data
        $category = Category::factory()->create();
        $label = Label::factory()->create();
        $domain = Domain::factory()->create();
        $grade = Grade::factory()->create();
        $mention = Mention::factory()->create();

        $establishment1 = Establishment::factory()->create([
            'name' => 'Test University 1',
            'abbreviation' => 'TU1',
            'description' => 'Test description 1',
            'category_id' => $category->id,
            'student_count' => 1000,
            'success_rate' => 85.5,
            'professional_insertion_rate' => 90.0,
            'first_habilitation_year' => 2020,
            'status' => 'Active',
            'international_partnerships' => 'Partnership with European universities',
            'address' => '123 Test Street',
            'region' => 'Test Region',
            'city' => 'Test City',
            'phone' => '+261 20 123 456',
            'email' => 'test1@university.edu',
            'website' => 'https://test1.university.edu',
        ]);

        $establishment2 = Establishment::factory()->create([
            'name' => 'Test University 2',
            'abbreviation' => 'TU2',
            'description' => 'Test description 2',
            'category_id' => $category->id,
            'student_count' => 1500,
            'success_rate' => 78.2,
            'professional_insertion_rate' => 85.5,
            'first_habilitation_year' => 2018,
            'status' => 'Active',
            'international_partnerships' => 'Partnership with African universities',
        ]);

        // Attach labels
        $establishment1->labels()->attach($label->id);
        $establishment2->labels()->attach($label->id);

        // Create program offerings
        $programOffering1 = ProgramOffering::factory()->create([
            'establishment_id' => $establishment1->id,
            'domain_id' => $domain->id,
            'grade_id' => $grade->id,
            'mention_id' => $mention->id,
            'tuition_fees_info' => '500,000 Ar/year',
            'program_duration_info' => '3 years',
        ]);

        $programOffering2 = ProgramOffering::factory()->create([
            'establishment_id' => $establishment2->id,
            'domain_id' => $domain->id,
            'grade_id' => $grade->id,
            'mention_id' => $mention->id,
            'tuition_fees_info' => '600,000 Ar/year',
            'program_duration_info' => '4 years',
        ]);

        // Create recent accreditation
        $reference = Reference::factory()->create();
        Accreditation::factory()->create([
            'program_offering_id' => $programOffering1->id,
            'reference_id' => $reference->id,
            'is_recent' => true,
            'accreditation_date' => now()->subDays(30),
            'reference_type' => 'Degree Authorization',
        ]);

        // Make API call
        $response = $this->getJson('/api/establishments/compare?' . http_build_query([
            'ids' => [$establishment1->id, $establishment2->id]
        ]));

        // Assert response structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'abbreviation',
                        'description',
                        'logo_url',
                        'category' => [
                            'id',
                            'name'
                        ],
                        'location' => [
                            'address',
                            'region',
                            'city',
                            'latitude',
                            'longitude'
                        ],
                        'contact' => [
                            'phone',
                            'email',
                            'website'
                        ],
                        'indicators' => [
                            'student_count',
                            'success_rate',
                            'professional_insertion_rate',
                            'first_habilitation_year',
                            'status',
                            'international_partnerships'
                        ],
                        'academic_offerings' => [
                            'total_programs',
                            'departments_count',
                            'domains_offered',
                            'grades_offered',
                            'tuition_fees',
                            'program_durations'
                        ],
                        'labels',
                        'recent_accreditation' => [
                            'has_recent'
                        ],
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);

        // Assert specific data for first establishment
        $response->assertJsonFragment([
            'name' => 'Test University 1',
            'abbreviation' => 'TU1',
            'description' => 'Test description 1'
        ]);

        // Assert indicators data
        $response->assertJsonFragment([
            'student_count' => 1000,
            'success_rate' => 85.5,
            'professional_insertion_rate' => 90.0,
            'first_habilitation_year' => 2020,
            'status' => 'Active',
            'international_partnerships' => 'Partnership with European universities'
        ]);

        // Assert location data
        $response->assertJsonFragment([
            'address' => '123 Test Street',
            'region' => 'Test Region',
            'city' => 'Test City'
        ]);

        // Assert contact data
        $response->assertJsonFragment([
            'phone' => '+261 20 123 456',
            'email' => 'test1@university.edu',
            'website' => 'https://test1.university.edu'
        ]);

        // Assert academic offerings
        $response->assertJsonFragment([
            'total_programs' => 1,
            'departments_count' => 1,
            'domains_offered' => [$domain->name],
            'grades_offered' => [$grade->name],
            'tuition_fees' => ['500,000 Ar/year'],
            'program_durations' => ['3 years']
        ]);

        // Assert recent accreditation
        $response->assertJsonFragment([
            'has_recent' => true,
            'reference_type' => 'Degree Authorization'
        ]);

        // Assert exactly 2 establishments returned
        $this->assertCount(2, $response->json('data'));
    }

    public function test_compare_establishments_validation()
    {
        // Test missing ids
        $response = $this->getJson('/api/establishments/compare');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ids']);

        // Test insufficient ids (less than 2)
        $response = $this->getJson('/api/establishments/compare?ids[]=1');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ids']);

        // Test too many ids (more than 5)
        $response = $this->getJson('/api/establishments/compare?' . http_build_query([
            'ids' => [1, 2, 3, 4, 5, 6]
        ]));
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ids']);
    }

    public function test_compare_establishments_with_non_existent_ids()
    {
        $response = $this->getJson('/api/establishments/compare?' . http_build_query([
            'ids' => [999, 1000]
        ]));
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ids.0', 'ids.1']);
    }

    public function test_compare_establishments_without_program_offerings()
    {
        $category = Category::factory()->create();
        $establishment1 = Establishment::factory()->create(['category_id' => $category->id]);
        $establishment2 = Establishment::factory()->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/establishments/compare?' . http_build_query([
            'ids' => [$establishment1->id, $establishment2->id]
        ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'total_programs' => 0,
                'departments_count' => 0,
                'domains_offered' => [],
                'grades_offered' => [],
                'tuition_fees' => null,
                'program_durations' => null
            ])
            ->assertJsonFragment([
                'has_recent' => false
            ]);
    }
}

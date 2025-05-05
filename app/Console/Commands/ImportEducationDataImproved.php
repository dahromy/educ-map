<?php

namespace App\Console\Commands;

use App\Models\Accreditation;
use App\Models\Category;
use App\Models\Department;
use App\Models\DoctoralSchoolAffiliation;
use App\Models\Domain;
use App\Models\Establishment;
use App\Models\Grade;
use App\Models\Mention;
use App\Models\ProgramOffering;
use App\Models\Reference;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportEducationDataImproved extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:education-data-improved {file? : Path to the JSON file} {--validate : Just validate without importing} {--dry-run : Show what would happen without making changes} {--no-geo : Skip geolocation lookups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import education data from JSON file into the database (improved version)';

    /**
     * Statistics for command output
     */
    private $stats = [
        'establishments' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'departments' => ['created' => 0, 'updated' => 0],
        'domains' => ['created' => 0, 'skipped' => 0],
        'grades' => ['created' => 0],
        'mentions' => ['created' => 0],
        'program_offerings' => ['created' => 0, 'updated' => 0],
        'references' => ['created' => 0, 'updated' => 0],
        'accreditations' => ['created' => 0],
        'geolocation' => ['success' => 0, 'failed' => 0],
        'categories' => ['created' => 0, 'updated' => 0],
        'affiliations' => ['created' => 0]
    ];

    /**
     * Common grade mappings
     */
    private $gradeNormalizations = [];

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Initialize grade normalizations
        $this->gradeNormalizations = [
            'licence' => ['Licence'],
            'master' => ['Master'],
            'doctorat' => ['Doctorat'],
            'licence et master' => ['Licence', 'Master'],
            'licence et master professionnels' => ['Licence', 'Master'],
            'master et doctorat' => ['Master', 'Doctorat'],
            'licence professionnelle' => ['Licence'],
            'master professionnel' => ['Master'],
            'master recherche' => ['Master'],
            'licence et arts, lettres et sciences humaines/licence' => ['Licence'],
        ];
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Determine the file path
        $filePath = $this->argument('file') ?? storage_path('app/private/data.json');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $jsonData = json_decode(file_get_contents($filePath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Invalid JSON file: " . json_last_error_msg());
            return 1;
        }

        // Filter out empty items
        $jsonData = array_filter($jsonData, function ($item) {
            return !empty($item) && isset($item['name']) && isset($item['category']);
        });

        $this->info("Starting import of " . count($jsonData) . " establishments");

        if ($this->option('validate')) {
            return $this->validateData($jsonData);
        }

        // Ensure we have a progress bar
        $bar = $this->output->createProgressBar(count($jsonData));
        $bar->start();

        // Start transaction only if not in dry run mode
        if (!$this->option('dry-run')) {
            DB::beginTransaction();
        }

        try {
            foreach ($jsonData as $index => $item) {
                // Process each establishment with a catch for individual entries
                try {
                    $this->processEstablishment($item);
                } catch (\Exception $e) {
                    $establishmentName = $item['name'] ?? 'Unknown';
                    $this->error("\nError processing establishment #{$index} ({$establishmentName}): " . $e->getMessage());
                    if ($this->option('dry-run')) {
                        // In dry-run mode, continue with other establishments
                        $this->stats['establishments']['skipped']++;
                    } else {
                        // In real import mode, throw to trigger rollback
                        throw $e;
                    }
                }

                $bar->advance();
            }

            if (!$this->option('dry-run')) {
                DB::commit();
                $this->info("\nCommitted all changes to the database.");
            } else {
                $this->info("\nDry run completed. No changes were made to the database.");
            }

            $bar->finish();

            $this->newLine(2);
            $this->displayStats();

            return 0;
        } catch (\Exception $e) {
            if (!$this->option('dry-run')) {
                DB::rollBack();
                $this->error("\nImport failed and all changes were rolled back: " . $e->getMessage());
            } else {
                $this->error("\nDry run failed: " . $e->getMessage());
            }

            Log::error("Import failed: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return 1;
        }
    }

    /**
     * Validate the data without importing.
     */
    private function validateData(array $data)
    {
        $this->info("Validating " . count($data) . " records...");
        $issues = 0;

        foreach ($data as $index => $item) {
            $itemIssues = [];

            // Check for required fields
            if (empty($item['name'])) {
                $itemIssues[] = "Missing name";
            }

            if (empty($item['category'])) {
                $itemIssues[] = "Missing category";
            }

            // Validate domains if present
            if (!empty($item['domains'])) {
                foreach ($item['domains'] as $dIndex => $domain) {
                    if (empty($domain['domain_name'])) {
                        $itemIssues[] = "Domain at index $dIndex missing domain_name";
                    }

                    if (empty($domain['grade'])) {
                        $itemIssues[] = "Domain '{$domain['domain_name']}' missing grade";
                    }

                    if (empty($domain['mentions']) || !is_array($domain['mentions'])) {
                        $itemIssues[] = "Domain '{$domain['domain_name']}' missing mentions array";
                    }

                    // Validate reference if present
                    if (!empty($domain['reference'])) {
                        $ref = $domain['reference'];
                        if (empty($ref['decree_number'])) {
                            $itemIssues[] = "Domain '{$domain['domain_name']}' reference missing decree_number";
                        }
                    }
                }
            }

            // Validate departments if present
            if (!empty($item['departments'])) {
                foreach ($item['departments'] as $dIndex => $dept) {
                    if (empty($dept['department_name'])) {
                        $itemIssues[] = "Department at index $dIndex missing department_name";
                    }

                    // Check domains within departments
                    if (!empty($dept['domains'])) {
                        foreach ($dept['domains'] as $domIndex => $domain) {
                            if (empty($domain['domain_name'])) {
                                $itemIssues[] = "Domain at index $domIndex in department '{$dept['department_name']}' missing domain_name";
                            }

                            if (empty($domain['grade'])) {
                                $itemIssues[] = "Domain '{$domain['domain_name']}' in department '{$dept['department_name']}' missing grade";
                            }

                            if (empty($domain['mentions']) || !is_array($domain['mentions'])) {
                                $itemIssues[] = "Domain '{$domain['domain_name']}' in department '{$dept['department_name']}' missing mentions array";
                            }
                        }
                    }
                }
            }

            if (!empty($itemIssues)) {
                $issues++;
                $this->warn("Issues with item " . ($item['id'] ?? $index) . " ({$item['name']}): ");
                foreach ($itemIssues as $issue) {
                    $this->line(" - $issue");
                }
            }
        }

        if ($issues > 0) {
            $this->error("Validation completed with $issues items having issues.");
            return 1;
        } else {
            $this->info("Validation completed successfully. All data appears valid.");
            return 0;
        }
    }

    /**
     * Process an establishment entry from the JSON data.
     */
    private function processEstablishment(array $item)
    {
        if (empty($item['name'])) {
            $this->stats['establishments']['skipped']++;
            return;
        }

        // Find or create category
        $category = $this->findOrCreateCategory($item['category']);

        if (!$this->option('dry-run')) {
            $this->newLine();
            $this->info("Processing establishment: {$item['name']}");
        }

        // Normalize establishment name to avoid duplicates
        $normalizedName = $this->normalizeEstablishmentName($item['name']);

        // Find existing establishment or create a new one
        $establishment = Establishment::firstOrNew([
            'name' => $normalizedName,
        ]);

        $isNew = !$establishment->exists;

        // Update establishment fields
        $establishment->abbreviation = $item['abbreviation'] ?? null;
        $establishment->category_id = $category->id;
        $establishment->address = $item['location'] ?? null;

        // If we have a location but no coordinates and geolocation is enabled
        if (
            !$this->option('no-geo') &&
            !empty($establishment->address) &&
            (empty($establishment->latitude) || empty($establishment->longitude))
        ) {
            $this->fetchGeoLocation($establishment);
        }

        // Save establishment (either as new or update) if not dry run
        if (!$this->option('dry-run')) {
            $establishment->save();
            $this->info("  - " . ($isNew ? "Created" : "Updated") . " establishment: {$establishment->name}");
        }

        // Update stats
        if ($isNew) {
            $this->stats['establishments']['created']++;
        } else {
            $this->stats['establishments']['updated']++;
        }

        // Process doctoral school affiliations if available
        if (!empty($item['institutions'])) {
            $this->processAffiliations($establishment, $item['institutions']);
        }

        // Process departments if available
        if (!empty($item['departments'])) {
            $this->processDepartments($establishment, $item['departments']);
        }

        // Process domains and program offerings directly attached to establishment
        if (!empty($item['domains'])) {
            $this->processDomains($establishment, $item['domains']);
        }
    }

    /**
     * Normalize establishment name to avoid duplicates
     */
    private function normalizeEstablishmentName(string $name): string
    {
        // Convert to uppercase for consistency with abbreviations often in uppercase
        $name = trim($name);

        // Remove extra spaces
        $name = preg_replace('/\s+/', ' ', $name);

        return $name;
    }

    /**
     * Process affiliations for doctoral schools
     */
    private function processAffiliations(Establishment $establishment, array $affiliations)
    {
        if (!class_exists(DoctoralSchoolAffiliation::class)) {
            if (!$this->option('dry-run')) {
                $this->warn("  - DoctoralSchoolAffiliation model not found. Skipping affiliations.");
            }
            return;
        }

        foreach ($affiliations as $institutionName) {
            if (empty($institutionName))
                continue;

            $affiliation = DoctoralSchoolAffiliation::firstOrNew([
                'establishment_id' => $establishment->id,
                'institution_name' => $institutionName
            ]);

            $isNew = !$affiliation->exists;

            if (!$this->option('dry-run') && $isNew) {
                $affiliation->save();
                $this->info("  - Created affiliation with: {$institutionName}");
                $this->stats['affiliations']['created']++;
            } elseif ($isNew) {
                $this->stats['affiliations']['created']++;
            }
        }
    }

    /**
     * Find or create a category.
     */
    private function findOrCreateCategory(string $categoryName): Category
    {
        $normalizedName = trim($categoryName);
        $category = Category::where('category_name', $normalizedName)->first();

        if (!$category) {
            $category = new Category();
            $category->category_name = $normalizedName;
            $isNew = true;
        } else {
            $isNew = false;
        }

        if ($isNew && !$this->option('dry-run')) {
            $category->save();
            $this->stats['categories']['created']++;
            $this->info("  - Created category: {$category->category_name}");
        } elseif ($isNew) {
            $this->stats['categories']['created']++;
        } else {
            $this->stats['categories']['updated']++;
        }

        return $category;
    }

    /**
     * Process departments for an establishment.
     */
    private function processDepartments(Establishment $establishment, array $departments)
    {
        foreach ($departments as $deptData) {
            if (empty($deptData['department_name']))
                continue;

            $deptName = trim($deptData['department_name']);

            if (!$this->option('dry-run')) {
                $this->info("  - Processing department: {$deptName}");
            }

            $department = Department::firstOrNew([
                'name' => $deptName,
                'establishment_id' => $establishment->id
            ]);

            $isNew = !$department->exists;

            if (!$this->option('dry-run')) {
                $department->save();

                if ($isNew) {
                    $this->info("    - Created department: {$department->name}");
                    $this->stats['departments']['created']++;
                } else {
                    $this->info("    - Updated department: {$department->name}");
                    $this->stats['departments']['updated']++;
                }
            } else {
                if ($isNew) {
                    $this->stats['departments']['created']++;
                } else {
                    $this->stats['departments']['updated']++;
                }
            }

            // Process domains within the department if available
            if (!empty($deptData['domains'])) {
                $this->processDomains($establishment, $deptData['domains'], $department);
            }
        }
    }

    /**
     * Process domains and program offerings.
     */
    private function processDomains(Establishment $establishment, array $domains, ?Department $department = null)
    {
        foreach ($domains as $domainData) {
            if (empty($domainData['domain_name'])) {
                $this->stats['domains']['skipped']++;
                continue;
            }

            $domainName = trim($domainData['domain_name']);

            if (!$this->option('dry-run')) {
                $this->info("  - Processing domain: {$domainName}");
            }

            // Find or create domain
            $domain = Domain::firstOrNew(['name' => $domainName]);
            $domainIsNew = !$domain->exists;

            if (!$this->option('dry-run')) {
                $domain->save();
                if ($domainIsNew) {
                    $this->info("    - Created domain: {$domain->name}");
                    $this->stats['domains']['created']++;
                }
            } else if ($domainIsNew) {
                $this->stats['domains']['created']++;
            }

            // Process grade and mentions
            if (!empty($domainData['grade']) && !empty($domainData['mentions'])) {
                // Process grades - handle both single grades and combined grades (e.g. "Licence et Master")
                $gradeString = strtolower(trim($domainData['grade']));
                $gradeNames = $this->normalizeGradeName($gradeString);

                foreach ($gradeNames as $gradeName) {
                    if (!$this->option('dry-run')) {
                        $this->info("    - Processing grade: {$gradeName}");
                    }

                    $grade = Grade::firstOrNew(['name' => $gradeName]);
                    $gradeIsNew = !$grade->exists;

                    if (!$this->option('dry-run')) {
                        $grade->save();
                        if ($gradeIsNew) {
                            $this->info("      - Created grade: {$grade->name}");
                            $this->stats['grades']['created']++;
                        }
                    } else if ($gradeIsNew) {
                        $this->stats['grades']['created']++;
                    }

                    // Process each mention for this grade
                    foreach ($domainData['mentions'] as $mentionName) {
                        if (empty($mentionName))
                            continue;

                        $mentionName = trim($mentionName);

                        if (!$this->option('dry-run')) {
                            $this->info("      - Processing mention: {$mentionName}");
                        }

                        // Find or create mention
                        $mention = Mention::firstOrNew(['name' => $mentionName]);
                        $mentionIsNew = !$mention->exists;

                        if (!$this->option('dry-run')) {
                            $mention->save();
                            if ($mentionIsNew) {
                                $this->info("        - Created mention: {$mention->name}");
                                $this->stats['mentions']['created']++;
                            }
                        } else if ($mentionIsNew) {
                            $this->stats['mentions']['created']++;
                        }

                        // Create or update program offering
                        $this->createProgramOffering(
                            $establishment,
                            $domain,
                            $grade,
                            $mention,
                            $department,
                            $domainData
                        );
                    }
                }
            }
        }
    }

    /**
     * Create a program offering and associated records
     */
    private function createProgramOffering(
        Establishment $establishment,
        Domain $domain,
        Grade $grade,
        Mention $mention,
        ?Department $department,
        array $domainData
    ) {
        // Find or create program offering
        $programOffering = ProgramOffering::firstOrNew([
            'establishment_id' => $establishment->id,
            'domain_id' => $domain->id,
            'grade_id' => $grade->id,
            'mention_id' => $mention->id,
        ]);

        $programIsNew = !$programOffering->exists;

        // Set department if available
        if ($department) {
            $programOffering->department_id = $department->id;
        }

        // Check for additional information
        if (!empty($domainData['tuition_fees_info'])) {
            $programOffering->tuition_fees_info = $domainData['tuition_fees_info'];
        }

        if (!empty($domainData['program_duration_info'])) {
            $programOffering->program_duration_info = $domainData['program_duration_info'];
        }

        if (!$this->option('dry-run')) {
            $programOffering->save();
            if ($programIsNew) {
                $this->info("        - Created program offering: {$domain->name} - {$grade->name} - {$mention->name}");
            } else {
                $this->info("        - Updated program offering: {$domain->name} - {$grade->name} - {$mention->name}");
            }
        }

        if ($programIsNew) {
            $this->stats['program_offerings']['created']++;
        } else {
            $this->stats['program_offerings']['updated']++;
        }

        // Process reference/accreditation if available
        if (!empty($domainData['reference'])) {
            $this->processReference($programOffering, $domainData['reference']);
        }
    }

    /**
     * Normalize a grade string into an array of standard grade names
     */
    private function normalizeGradeName(string $gradeString): array
    {
        $normalizedGrades = [];
        $lowerGradeString = Str::lower(trim($gradeString));

        // Check if we have a direct mapping
        if (isset($this->gradeNormalizations[$lowerGradeString])) {
            return $this->gradeNormalizations[$lowerGradeString];
        }

        // Check for partial matches
        foreach ($this->gradeNormalizations as $pattern => $grades) {
            if (Str::contains($lowerGradeString, $pattern)) {
                return $grades;
            }
        }

        // If no match is found, try to parse common patterns
        if (Str::contains($lowerGradeString, 'licence')) {
            $normalizedGrades[] = 'Licence';
        }
        if (Str::contains($lowerGradeString, 'master')) {
            $normalizedGrades[] = 'Master';
        }
        if (Str::contains($lowerGradeString, 'doctorat')) {
            $normalizedGrades[] = 'Doctorat';
        }

        // If still no match, use the original string but properly capitalized
        if (empty($normalizedGrades)) {
            $normalizedGrades[] = Str::ucfirst($lowerGradeString);
        }

        return $normalizedGrades;
    }

    /**
     * Process reference and create accreditation.
     */
    private function processReference(ProgramOffering $programOffering, array $referenceData)
    {
        // Skip if we don't have a decree number (essential to identify the reference)
        if (empty($referenceData['decree_number'])) {
            if (!$this->option('dry-run')) {
                $this->warn("        - Skipping reference with no decree number");
            }
            return;
        }

        $decreeNumber = trim($referenceData['decree_number']);

        if (!$this->option('dry-run')) {
            $this->info("        - Processing reference: {$decreeNumber}");
        }

        // Create a title from decree number and authority
        $title = $decreeNumber;
        if (!empty($referenceData['authority'])) {
            $title = trim($referenceData['authority']) . ' - ' . $title;
        }

        // Create or update reference
        $reference = Reference::firstOrNew(['title' => $title]);
        $referenceIsNew = !$reference->exists;

        // Update reference fields
        if (!empty($referenceData['date'])) {
            try {
                $reference->main_date = Carbon::parse($referenceData['date'])->format('Y-m-d');
            } catch (\Exception $e) {
                if (!$this->option('dry-run')) {
                    $this->warn("          - Invalid date format: {$referenceData['date']}");
                }
            }
        }

        // Create description with the decree number and authority
        $description = "Décret N° " . $decreeNumber;
        if (!empty($referenceData['authority'])) {
            $description .= " - " . trim($referenceData['authority']);
        }
        $reference->description = $description;

        if (!empty($referenceData['document_url'])) {
            $reference->document_url = $referenceData['document_url'];
        }

        if (!$this->option('dry-run')) {
            $reference->save();
            if ($referenceIsNew) {
                $this->info("          - Created reference: {$reference->title}");
            } else {
                $this->info("          - Updated reference: {$reference->title}");
            }
        }

        if ($referenceIsNew) {
            $this->stats['references']['created']++;
        } else {
            $this->stats['references']['updated']++;
        }

        // Create accreditation linking program offering to reference if it doesn't exist
        $accreditation = Accreditation::firstOrNew([
            'program_offering_id' => $programOffering->id,
            'reference_id' => $reference->id
        ]);

        $accreditationIsNew = !$accreditation->exists;

        if ($accreditationIsNew) {
            // Set accreditation date from reference if available
            if (!empty($reference->main_date)) {
                $accreditation->accreditation_date = $reference->main_date;
            }

            // Set reference type to "decree" since it appears these are all decrees
            $accreditation->reference_type = 'decree';

            // Determine if this is recent (within the last year)
            if (!empty($accreditation->accreditation_date)) {
                try {
                    $accreditationDate = is_string($accreditation->accreditation_date)
                        ? Carbon::parse($accreditation->accreditation_date)
                        : $accreditation->accreditation_date;

                    $accreditation->is_recent = $accreditationDate->isAfter(Carbon::now()->subYear());
                } catch (\Exception $e) {
                    $accreditation->is_recent = false;
                    if (!$this->option('dry-run')) {
                        $this->warn("          - Error determining recency: {$e->getMessage()}");
                    }
                }
            }

            if (!$this->option('dry-run')) {
                $accreditation->save();
                $this->info("          - Created accreditation");

                if (!empty($accreditation->accreditation_date)) {
                    try {
                        $dateStr = is_string($accreditation->accreditation_date)
                            ? $accreditation->accreditation_date
                            : $accreditation->accreditation_date->format('Y-m-d');

                        $this->info("            with date: {$dateStr}");
                    } catch (\Exception $e) {
                        $this->warn("            with invalid date");
                    }
                }
            }

            $this->stats['accreditations']['created']++;
        }
    }

    /**
     * Fetch geolocation data using OpenStreetMap Nominatim API.
     */
    private function fetchGeoLocation(Establishment $establishment)
    {
        if ($this->option('dry-run')) {
            $this->stats['geolocation']['success']++;
            return;
        }

        try {
            // Add "Madagascar" to the query for better results
            $searchQuery = trim($establishment->address);
            if (!empty($searchQuery)) {
                if (!Str::contains(Str::lower($searchQuery), 'madagascar')) {
                    $searchQuery .= ' Madagascar';
                }

                $this->info("    - Fetching geolocation for: {$searchQuery}");

                $response = Http::withHeaders([
                    'User-Agent' => 'EducMap/1.0 (https://educ-map.mg; contact@educ-map.mg)',
                ])->get('https://nominatim.openstreetmap.org/search', [
                            'format' => 'json',
                            'q' => $searchQuery,
                            'limit' => 1,
                            'addressdetails' => 1,
                            'accept-language' => 'fr',
                        ]);

                if ($response->successful() && count($response->json()) > 0) {
                    $result = $response->json()[0];
                    $establishment->latitude = $result['lat'];
                    $establishment->longitude = $result['lon'];

                    // Add more detailed address information if available
                    if (isset($result['address'])) {
                        $address = $result['address'];

                        // Extract region information
                        if (!empty($address['state'])) {
                            $establishment->region = $address['state'];
                        }

                        // Extract city information
                        if (!empty($address['city'])) {
                            $establishment->city = $address['city'];
                        } elseif (!empty($address['town'])) {
                            $establishment->city = $address['town'];
                        } elseif (!empty($address['village'])) {
                            $establishment->city = $address['village'];
                        }
                    }

                    $this->stats['geolocation']['success']++;
                    $this->info("      - Found coordinates: {$establishment->latitude}, {$establishment->longitude}");
                    if (!empty($establishment->region)) {
                        $this->info("      - Region: {$establishment->region}");
                    }
                    if (!empty($establishment->city)) {
                        $this->info("      - City: {$establishment->city}");
                    }

                    // Respect Nominatim usage policy by sleeping
                    sleep(1);
                } else {
                    $this->stats['geolocation']['failed']++;
                    $this->warn("      - Couldn't find geolocation for: {$establishment->address}");
                }
            } else {
                $this->stats['geolocation']['failed']++;
                $this->warn("      - No address provided for geolocation");
            }
        } catch (\Exception $e) {
            $this->stats['geolocation']['failed']++;
            $this->warn("      - Error while fetching geolocation: " . $e->getMessage());
        }
    }

    /**
     * Display statistics about the import process.
     */
    private function displayStats()
    {
        $this->info('Import Statistics:');
        $this->info('=================');

        $this->info('Categories: ' .
            $this->stats['categories']['created'] . ' created, ' .
            $this->stats['categories']['updated'] . ' updated');

        $this->info('Establishments: ' .
            $this->stats['establishments']['created'] . ' created, ' .
            $this->stats['establishments']['updated'] . ' updated, ' .
            $this->stats['establishments']['skipped'] . ' skipped');

        $this->info('Departments: ' .
            $this->stats['departments']['created'] . ' created, ' .
            $this->stats['departments']['updated'] . ' updated');

        $this->info('Domains: ' .
            $this->stats['domains']['created'] . ' created, ' .
            $this->stats['domains']['skipped'] . ' skipped');

        $this->info('Grades: ' .
            $this->stats['grades']['created'] . ' created');

        $this->info('Mentions: ' .
            $this->stats['mentions']['created'] . ' created');

        $this->info('Program Offerings: ' .
            $this->stats['program_offerings']['created'] . ' created, ' .
            $this->stats['program_offerings']['updated'] . ' updated');

        $this->info('References: ' .
            $this->stats['references']['created'] . ' created, ' .
            $this->stats['references']['updated'] . ' updated');

        $this->info('Accreditations: ' .
            $this->stats['accreditations']['created'] . ' created');

        $this->info('Affiliations: ' .
            $this->stats['affiliations']['created'] . ' created');

        $this->info('Geolocation: ' .
            $this->stats['geolocation']['success'] . ' successful, ' .
            $this->stats['geolocation']['failed'] . ' failed');
    }
}

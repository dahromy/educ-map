<?php

namespace App\Console\Commands;

use App\Models\Accreditation;
use App\Models\Category;
use App\Models\Department;
use App\Models\Domain;
use App\Models\Establishment;
use App\Models\Grade;
use App\Models\Mention;
use App\Models\ProgramOffering;
use App\Models\Reference;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ImportEducationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:education-data {file? : Path to the JSON file} {--validate : Just validate without importing} {--dry-run : Show what would happen without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import education data from JSON file into the database';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Initialize grade normalizations
        $this->gradeNormalizations = [
            'licence' => 'Licence',
            'master' => 'Master',
            'doctorat' => 'Doctorat',
            'licence et master' => ['Licence', 'Master'],
            'master et doctorat' => ['Master', 'Doctorat'],
            'licence et master professionnels' => ['Licence', 'Master'],
            'licence professionnelle' => 'Licence',
            'master professionnel' => 'Master',
        ];
    }

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
        'categories' => ['created' => 0],
        'affiliations' => ['created' => 0]
    ];

    /**
     * Common grade names and their normalized versions
     */
    private $gradeNormalizations = [
        'licence' => 'Licence',
        'master' => 'Master',
        'doctorat' => 'Doctorat',
        'licence et master' => ['Licence', 'Master'],
        'master et doctorat' => ['Master', 'Doctorat'],
        'licence et master professionnels' => ['Licence', 'Master'],
        'licence professionnelle' => 'Licence',
        'master professionnel' => 'Master',
    ];

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

        // Clean up domain entries with missing required fields
        foreach ($jsonData as $key => $item) {
            if (!empty($item['domains'])) {
                $jsonData[$key]['domains'] = array_filter($item['domains'], function ($domain) {
                    return !empty($domain['domain_name']) && !empty($domain['grade']) && !empty($domain['mentions']);
                });

                // Clean up references with missing decree numbers
                foreach ($jsonData[$key]['domains'] as $dKey => $domain) {
                    if (isset($domain['reference']) && empty($domain['reference']['decree_number'])) {
                        unset($jsonData[$key]['domains'][$dKey]['reference']);
                    }
                }
            }
        }

        $this->info("Starting import of " . count($jsonData) . " establishments");

        if ($this->option('validate')) {
            return $this->validateData($jsonData);
        }

        $bar = $this->output->createProgressBar(count($jsonData));
        $bar->start();

        // Start transaction only if not in dry run mode
        if (!$this->option('dry-run')) {
            DB::beginTransaction();
        }

        try {
            foreach ($jsonData as $item) {
                // Process the establishment
                $this->processEstablishment($item);
                $bar->advance();
            }

            if (!$this->option('dry-run')) {
                DB::commit();
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
            }

            $this->error("\nImport failed: " . $e->getMessage());
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
        // Find or create category
        $category = $this->findOrCreateCategory($item['category']);

        if (!$this->option('dry-run')) {
            $this->info("\nProcessing establishment: {$item['name']}");
        }

        // Find existing establishment or create a new one
        $establishment = Establishment::firstOrNew([
            'name' => $item['name'],
        ]);

        $isNew = !$establishment->exists;

        // Update establishment fields
        $establishment->abbreviation = $item['abbreviation'] ?? null;
        $establishment->category_id = $category->id;
        $establishment->address = $item['location'] ?? '';

        // If we have a location but no coordinates, try to get them
        if (
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

        // Process departments if available
        if (!empty($item['departments'])) {
            $this->processDepartments($establishment, $item['departments']);
        }

        // Process domains and program offerings
        if (!empty($item['domains'])) {
            $this->processDomains($establishment, $item['domains']);
        }
    }    /**
         * Find or create a category.
         */
    private function findOrCreateCategory(string $categoryName): Category
    {
        $category = Category::firstOrNew(['category_name' => $categoryName]);
        $isNew = !$category->exists;

        if ($isNew && !$this->option('dry-run')) {
            $category->save();
            $this->stats['categories']['created']++;
            $this->info("  - Created category: {$category->category_name}");
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

            if (!$this->option('dry-run')) {
                $this->info("  - Processing department: {$deptData['department_name']}");
            }

            $department = Department::firstOrNew([
                'name' => $deptData['department_name'],
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
                $this->stats['domains']['skipped'] = ($this->stats['domains']['skipped'] ?? 0) + 1;
                continue;
            }

            if (!$this->option('dry-run')) {
                $this->info("  - Processing domain: {$domainData['domain_name']}");
            }

            // Find or create domain
            $domain = Domain::firstOrNew(['name' => $domainData['domain_name']]);
            $domainIsNew = !$domain->exists;

            if ($domainIsNew && !$this->option('dry-run')) {
                $domain->save();
                $this->stats['domains']['created']++;
                $this->info("    - Created domain: {$domain->name}");
            }

            // Process grade and mentions
            if (!empty($domainData['grade']) && !empty($domainData['mentions'])) {
                $gradeNames = explode(' et ', $domainData['grade']);

                foreach ($gradeNames as $gradeName) {
                    $gradeName = trim($gradeName);
                    if (!$this->option('dry-run')) {
                        $this->info("    - Processing grade: {$gradeName}");
                    }

                    $grade = Grade::firstOrNew(['name' => $gradeName]);
                    $gradeIsNew = !$grade->exists;

                    if ($gradeIsNew && !$this->option('dry-run')) {
                        $grade->save();
                        $this->stats['grades']['created']++;
                        $this->info("      - Created grade: {$grade->name}");
                    }

                    foreach ($domainData['mentions'] as $mentionName) {
                        // Skip empty mentions
                        if (empty($mentionName))
                            continue;

                        if (!$this->option('dry-run')) {
                            $this->info("      - Processing mention: {$mentionName}");
                        }                        // Find or create mention
                        $mention = Mention::firstOrNew(['name' => $mentionName]);
                        $mentionIsNew = !$mention->exists;

                        if ($mentionIsNew && !$this->option('dry-run')) {
                            $mention->save();
                            $this->stats['mentions']['created']++;
                            $this->info("        - Created mention: {$mention->name}");
                        }

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
                }
            }
        }
    }    /**
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

        if (!$this->option('dry-run')) {
            $this->info("        - Processing reference: {$referenceData['decree_number']}");
        }

        // Create a title from decree number and authority
        $title = $referenceData['decree_number'];
        if (!empty($referenceData['authority'])) {
            $title = $referenceData['authority'] . ' - ' . $title;
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
        $description = "Décret N° " . $referenceData['decree_number'];
        if (!empty($referenceData['authority'])) {
            $description .= " - " . $referenceData['authority'];
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
                    $dateString = is_string($accreditation->accreditation_date)
                        ? $accreditation->accreditation_date
                        : $accreditation->accreditation_date->format('Y-m-d');
                    $accreditationDate = Carbon::parse($dateString);
                    $accreditation->is_recent = $accreditationDate->isAfter(Carbon::now()->subYear());
                } catch (\Exception $e) {
                    $accreditation->is_recent = false;
                    if (!$this->option('dry-run')) {
                        $this->warn("          - Error parsing accreditation date: {$e->getMessage()}");
                    }
                }
            }

            if (!$this->option('dry-run')) {
                $accreditation->save();
                $this->info("          - Created accreditation with date: " .
                    (!empty($accreditation->accreditation_date) ?
                        (is_string($accreditation->accreditation_date) ?
                            $accreditation->accreditation_date :
                            $accreditation->accreditation_date->format('Y-m-d'))
                        : 'None'));
            }

            $this->stats['accreditations']['created']++;
        }
    }    /**
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
            if (!str_contains(strtolower($searchQuery), 'madagascar')) {
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
            $this->stats['categories']['created'] . ' created');

        $this->info('Establishments: ' .
            $this->stats['establishments']['created'] . ' created, ' .
            $this->stats['establishments']['updated'] . ' updated');

        $this->info('Domains: ' .
            $this->stats['domains']['created'] . ' created');

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

        $this->info('Geolocation: ' .
            $this->stats['geolocation']['success'] . ' successful, ' .
            $this->stats['geolocation']['failed'] . ' failed');
    }
}

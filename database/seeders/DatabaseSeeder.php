<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Establishment;
use App\Models\Domain;
use App\Models\Grade;
use App\Models\Label;
use App\Models\Mention;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@educ-map.mg',
            'password' => Hash::make('password'),
            'roles' => ['ROLE_ADMIN'],
        ]);

        // Create establishment user
        User::create([
            'name' => 'Establishment Manager',
            'email' => 'manager@educ-map.mg',
            'password' => Hash::make('password'),
            'roles' => ['ROLE_ESTABLISHMENT'],
        ]);

        // Create regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'user@educ-map.mg',
            'password' => Hash::make('password'),
            'roles' => ['ROLE_USER'],
        ]);

        // Create categories
        $categories = [
            ['category_name' => 'Public University', 'description' => 'State-funded universities'],
            ['category_name' => 'Private University', 'description' => 'Privately funded universities'],
            ['category_name' => 'Public Institute', 'description' => 'State-funded specialized institutes'],
            ['category_name' => 'Private Institute', 'description' => 'Privately funded specialized institutes'],
            ['category_name' => 'Research Center', 'description' => 'Centers focused on research activities'],
            ['category_name' => 'Doctoral School', 'description' => 'Schools offering doctoral programs'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create domains (fields of study)
        $domains = [
            ['name' => 'Computer Science', 'description' => 'Study of computers and computational systems'],
            ['name' => 'Engineering', 'description' => 'Application of scientific knowledge to design and build systems'],
            ['name' => 'Medicine', 'description' => 'Science and practice of diagnosing, treating, and preventing disease'],
            ['name' => 'Business', 'description' => 'Commercial, industrial, and professional activities'],
            ['name' => 'Arts and Humanities', 'description' => 'Study of human culture and creative expressions'],
            ['name' => 'Education', 'description' => 'Facilitation of learning and acquisition of knowledge'],
        ];

        foreach ($domains as $domain) {
            Domain::create($domain);
        }

        // Create grades (academic levels)
        $grades = [
            ['name' => 'Licence', 'level' => 1, 'description' => 'Bachelor\'s degree equivalent'],
            ['name' => 'Master', 'level' => 2, 'description' => 'Master\'s degree'],
            ['name' => 'Doctorat', 'level' => 3, 'description' => 'Doctoral degree'],
        ];

        foreach ($grades as $grade) {
            Grade::create($grade);
        }

        // Create mentions (specializations)
        $mentions = [
            ['name' => 'Computer Science', 'description' => 'Study of algorithms and computational systems'],
            ['name' => 'Information Systems', 'description' => 'Study of systems for information management'],
            ['name' => 'Civil Engineering', 'description' => 'Design and construction of physical built environment'],
            ['name' => 'Electrical Engineering', 'description' => 'Study of electricity, electronics, and electromagnetism'],
            ['name' => 'General Medicine', 'description' => 'Practice of medical care'],
            ['name' => 'Business Administration', 'description' => 'Management of business operations'],
        ];

        foreach ($mentions as $mention) {
            Mention::create($mention);
        }

        // Create labels
        $labels = [
            ['name' => 'Excellence', 'description' => 'Institution recognized for excellence'],
            ['name' => 'International', 'description' => 'Institution with international programs or partnerships'],
            ['name' => 'Research-Focused', 'description' => 'Institution with emphasis on research'],
            ['name' => 'Accredited', 'description' => 'Institution with highest level of accreditation'],
        ];

        foreach ($labels as $label) {
            Label::create($label);
        }

        // Create sample establishments
        $establishments = [
            [
                'name' => 'University of Antananarivo',
                'abbreviation' => 'UA',
                'description' => 'The largest and oldest university in Madagascar',
                'category_id' => 1, // Public University
                'address' => 'Ankatso, 101 Antananarivo',
                'region' => 'Analamanga',
                'city' => 'Antananarivo',
                'latitude' => -18.9167,
                'longitude' => 47.5167,
                'phone' => '+261 20 22 326 39',
                'email' => 'contact@univ-antananarivo.mg',
                'website' => 'http://www.univ-antananarivo.mg',
                'logo_url' => 'http://example.com/ua-logo.png',
                'student_count' => 30000,
                'success_rate' => 75.5,
                'professional_insertion_rate' => 68.0,
                'first_habilitation_year' => 1961,
            ],
            [
                'name' => 'University of Toamasina',
                'abbreviation' => 'UT',
                'description' => 'Major university on the east coast of Madagascar',
                'category_id' => 1, // Public University
                'address' => 'Boulevard Philippon, Ambohijafy Toamasina',
                'region' => 'Atsinanana',
                'city' => 'Toamasina',
                'latitude' => -18.1496,
                'longitude' => 49.4037,
                'phone' => '+261 20 53 320 08',
                'email' => 'contact@univ-toamasina.mg',
                'website' => 'http://www.univ-toamasina.mg',
                'logo_url' => 'http://example.com/ut-logo.png',
                'student_count' => 15000,
                'success_rate' => 71.0,
                'professional_insertion_rate' => 65.0,
                'first_habilitation_year' => 1977,
            ],
            [
                'name' => 'Institut Supérieur de Technologie d\'Antananarivo',
                'abbreviation' => 'IST-T',
                'description' => 'Leading technology institute in Antananarivo',
                'category_id' => 3, // Public Institute
                'address' => 'BP 8122, Ampasampito, Antananarivo',
                'region' => 'Analamanga',
                'city' => 'Antananarivo',
                'latitude' => -18.8792,
                'longitude' => 47.5079,
                'phone' => '+261 20 22 415 71',
                'email' => 'contact@ist-tana.mg',
                'website' => 'http://www.ist-tana.mg',
                'logo_url' => 'http://example.com/ist-logo.png',
                'student_count' => 3500,
                'success_rate' => 82.5,
                'professional_insertion_rate' => 78.0,
                'first_habilitation_year' => 1992,
            ],
        ];

        foreach ($establishments as $establishmentData) {
            $establishment = Establishment::create($establishmentData);

            // Associate random labels with each establishment
            $labelIds = Label::inRandomOrder()->limit(rand(1, 3))->pluck('id')->toArray();
            $establishment->labels()->attach($labelIds);

            // Associate an establishment with the manager user
            if ($establishment->id === 1) {
                User::where('email', 'manager@educ-map.mg')->update(['associated_establishment' => $establishment->id]);
            }
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create Categories Table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create Labels Table
        Schema::create('labels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create Domains (Fields of Study) Table
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create Grades (Academic Levels) Table
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create Mentions (Program/Major/Specialization) Table
        Schema::create('mentions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create References (Accreditation Decree Details) Table
        Schema::create('references', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('main_date');
            $table->string('document_url')->nullable();
            $table->timestamps();
        });

        // Create Establishments Table
        Schema::create('establishments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbreviation')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            // Location fields
            $table->string('address')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Contact details
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_url')->nullable();

            // Indicators
            $table->integer('student_count')->nullable();
            $table->float('success_rate')->nullable();
            $table->float('professional_insertion_rate')->nullable();
            $table->integer('first_habilitation_year')->nullable();

            $table->timestamps();
        });

        // Create EstablishmentLabel Pivot Table
        Schema::create('establishment_labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->constrained()->onDelete('cascade');
            $table->foreignId('label_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['establishment_id', 'label_id']);
        });

        // Create Departments Table
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbreviation')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('establishment_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Create ProgramOfferings Table
        Schema::create('program_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('domain_id')->constrained()->onDelete('cascade');
            $table->foreignId('grade_id')->constrained()->onDelete('cascade');
            $table->foreignId('mention_id')->nullable()->constrained()->onDelete('set null');
            $table->text('tuition_fees_info')->nullable();
            $table->text('program_duration_info')->nullable();
            $table->timestamps();
        });

        // Create Accreditations Table (Junction between ProgramOffering and Reference)
        Schema::create('accreditations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_offering_id')->constrained()->onDelete('cascade');
            $table->foreignId('reference_id')->constrained()->onDelete('cascade');
            $table->string('reference_type')->nullable();
            $table->date('accreditation_date')->nullable();
            $table->boolean('is_recent')->default(false);
            $table->timestamps();
        });

        // Create DoctoralSchoolAffiliations Table
        Schema::create('doctoral_school_affiliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctoral_school_id')->constrained('establishments')->onDelete('cascade');
            $table->string('affiliated_institution_name');
            $table->timestamps();
        });

        // Modify Users Table to add roles and associated establishment
        Schema::table('users', function (Blueprint $table) {
            $table->json('roles')->nullable()->after('remember_token');
            $table->foreignId('associated_establishment')->nullable()->after('roles')
                ->constrained('establishments')->onDelete('set null');
        });

        // Create SearchHistory Table
        Schema::create('search_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('search_query_text')->nullable();
            $table->json('search_filters')->nullable();
            $table->timestamps();
        });

        // Create FaqItems Table
        Schema::create('faq_items', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Create OfficialDocuments Table
        Schema::create('official_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url_or_path');
            $table->string('type')->nullable();
            $table->foreignId('reference_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order to avoid foreign key constraints
        Schema::dropIfExists('official_documents');
        Schema::dropIfExists('faq_items');
        Schema::dropIfExists('search_histories');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['roles', 'associated_establishment']);
        });
        Schema::dropIfExists('doctoral_school_affiliations');
        Schema::dropIfExists('accreditations');
        Schema::dropIfExists('program_offerings');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('establishment_labels');
        Schema::dropIfExists('establishments');
        Schema::dropIfExists('references');
        Schema::dropIfExists('mentions');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('domains');
        Schema::dropIfExists('labels');
        Schema::dropIfExists('categories');
    }
};

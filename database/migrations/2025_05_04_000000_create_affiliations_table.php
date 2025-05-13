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
        Schema::create('affiliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->constrained()->onDelete('cascade');
            $table->string('institution_name');
            $table->text('description')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate affiliations
            $table->unique(['establishment_id', 'institution_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliations');
    }
};

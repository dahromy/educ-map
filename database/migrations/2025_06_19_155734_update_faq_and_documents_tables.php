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
        // Update faq_items table
        Schema::table('faq_items', function (Blueprint $table) {
            // Rename 'order' to 'sort_order' (order is a reserved keyword)
            $table->renameColumn('order', 'sort_order');
            // Add is_active column
            $table->boolean('is_active')->default(true);
        });

        // Update official_documents table  
        Schema::table('official_documents', function (Blueprint $table) {
            // Rename url_or_path to document_url and add document_path
            $table->renameColumn('url_or_path', 'document_url');
            $table->string('document_path')->nullable()->after('document_url');

            // Rename type to document_type
            $table->renameColumn('type', 'document_type');

            // Add missing columns
            $table->integer('file_size')->nullable()->after('document_type');
            $table->string('mime_type')->nullable()->after('file_size');
            $table->integer('sort_order')->default(0)->after('mime_type');
            $table->boolean('is_active')->default(true)->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse faq_items table changes
        Schema::table('faq_items', function (Blueprint $table) {
            $table->renameColumn('sort_order', 'order');
            $table->dropColumn('is_active');
        });

        // Reverse official_documents table changes
        Schema::table('official_documents', function (Blueprint $table) {
            $table->renameColumn('document_url', 'url_or_path');
            $table->dropColumn(['document_path', 'file_size', 'mime_type', 'sort_order', 'is_active']);
            $table->renameColumn('document_type', 'type');
        });
    }
};

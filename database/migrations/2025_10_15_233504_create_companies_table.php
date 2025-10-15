<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('industry_id')->nullable()->constrained('industries')->nullOnDelete();
            
            // Contact Information
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            
            // Branding
            $table->string('logo_url')->nullable();
            $table->string('cover_image_url')->nullable();
            
            // Company Details
            $table->text('description')->nullable();
            $table->string('company_size')->nullable(); // e.g., '1-10', '11-50', '51-200', etc.
            $table->integer('founded_year')->nullable();
            $table->string('headquarters_location')->nullable();
            
            // JSON Fields
            $table->json('locations')->nullable(); // Multiple office locations
            $table->json('social_links')->nullable(); // LinkedIn, Twitter, Facebook, etc.
            
            // Verification & Status
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('slug');
            $table->index('industry_id');
            $table->index(['is_verified', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

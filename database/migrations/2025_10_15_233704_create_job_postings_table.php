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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('posted_by_user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('industry_id')->nullable()->constrained('industries')->nullOnDelete();
            
            // Job Basic Information
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('benefits')->nullable();
            
            // Job Classification
            $table->enum('job_type', ['full_time', 'part_time', 'contract', 'temporary', 'internship', 'freelance']);
            $table->enum('experience_level', ['entry', 'junior', 'mid', 'senior', 'lead', 'executive']);
            $table->enum('location_type', ['onsite', 'remote', 'hybrid']);
            $table->string('location')->nullable();
            
            // Salary Information
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->string('salary_currency', 3)->default('USD');
            $table->enum('salary_period', ['hourly', 'monthly', 'yearly'])->default('yearly');
            $table->boolean('is_salary_visible')->default(true);
            
            // Job Details
            $table->integer('positions_available')->default(1);
            $table->date('application_deadline')->nullable();
            
            // Status & Visibility
            $table->enum('status', ['draft', 'pending', 'active', 'paused', 'closed', 'expired'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Featured Job
            $table->boolean('is_featured')->default(false);
            $table->timestamp('featured_until')->nullable();
            
            // Metrics
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('applications_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('slug');
            $table->index('status');
            $table->index('company_id');
            $table->index('industry_id');
            $table->index('published_at');
            $table->index(['status', 'published_at']);
            $table->index(['is_featured', 'featured_until']);
            
            // Full-text index for search
            $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};

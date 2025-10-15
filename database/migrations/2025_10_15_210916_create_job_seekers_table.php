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
        Schema::create('job_seekers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->string('current_job_title')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->string('current_location')->nullable();
            $table->decimal('expected_salary_min', 10, 2)->nullable();
            $table->decimal('expected_salary_max', 10, 2)->nullable();
            $table->string('salary_currency', 3)->default('USD');
            $table->integer('notice_period_days')->nullable();
            $table->date('available_from')->nullable();
            $table->string('resume_url')->nullable();
            $table->timestamp('resume_uploaded_at')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('github_url')->nullable();
            $table->json('preferred_locations')->nullable();
            $table->json('languages')->nullable();
            $table->boolean('is_profile_public')->default(true);
            $table->boolean('is_available')->default(true);
            $table->integer('profile_completion_percentage')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('user_id');
            $table->index('is_available');
            $table->index('is_profile_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_seekers');
    }
};

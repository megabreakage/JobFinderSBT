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
            $table->unsignedBigInteger('user_id')->unique();
            $table->text('bio')->nullable();
            $table->string('current_job_title')->nullable();
            $table->string('current_company')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->string('current_location')->nullable();
            $table->string('preferred_locations')->nullable();
            $table->decimal('expected_salary_min', 10, 2)->nullable();
            $table->decimal('expected_salary_max', 10, 2)->nullable();
            $table->string('salary_currency', 3)->default('KES');
            $table->enum('employment_type_preference', ['full-time', 'part-time', 'contract', 'freelance', 'internship'])->nullable();
            $table->boolean('open_to_remote')->default(false);
            $table->boolean('willing_to_relocate')->default(false);
            $table->string('resume_path')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->integer('profile_completion_percentage')->default(0);
            $table->boolean('is_profile_public')->default(true);
            $table->boolean('is_available')->default(true);
            $table->date('available_from')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['is_profile_public', 'is_available']);
            $table->index('current_location');
            $table->index('years_of_experience');
            $table->index('deleted_at');
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

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
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('posted_by_user_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->text('requirements')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('benefits')->nullable();
            $table->unsignedBigInteger('industry_id')->nullable();
            $table->enum('type', ['full-time', 'part-time', 'contract', 'freelance', 'internship']);
            $table->enum('experience_level', ['entry', 'mid', 'senior', 'executive'])->default('mid');
            $table->string('location');
            $table->boolean('is_remote')->default(false);
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_currency', 3)->default('KES');
            $table->enum('salary_period', ['hourly', 'daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->boolean('salary_negotiable')->default(false);
            $table->integer('positions_available')->default(1);
            $table->date('application_deadline')->nullable();
            $table->enum('status', ['draft', 'active', 'paused', 'closed', 'expired'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->date('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('company_id')->references('id')->on('companies')->restrictOnDelete();
            $table->foreignId('posted_by_user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreignId('industry_id')->references('id')->on('industries')->restrictOnDelete();

            $table->index(['status', 'is_featured', 'expires_at']);
            $table->index(['company_id', 'status']);
            $table->index(['industry_id', 'status']);
            $table->index('location');
            $table->index('deleted_at');
            $table->fullText(['title', 'description', 'requirements']);
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

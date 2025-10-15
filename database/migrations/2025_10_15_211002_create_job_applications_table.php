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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('job_posting_id');
            $table->unsignedBigInteger('job_seeker_id');
            $table->text('cover_letter')->nullable();
            $table->string('resume_path')->nullable();
            $table->json('additional_documents')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'shortlisted', 'interview_scheduled', 'interviewed', 'offered', 'hired', 'rejected', 'withdrawn'])->default('pending');
            $table->text('employer_notes')->nullable();
            $table->integer('rating')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('interview_scheduled_at')->nullable();
            $table->string('interview_location')->nullable();
            $table->text('interview_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('job_posting_id')->references('id')->on('job_postings')->onDelete('cascade');
            $table->foreign('job_seeker_id')->references('id')->on('job_seekers')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->restrictOnDelete();

            $table->unique(['job_posting_id', 'job_seeker_id', 'deleted_at']);
            $table->index(['status', 'created_at']);
            $table->index(['job_posting_id', 'status']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};

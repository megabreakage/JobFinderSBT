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
        Schema::create('saved_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_seeker_id');
            $table->unsignedBigInteger('job_posting_id');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('job_seeker_id')->references('id')->on('job_seekers')->restrictOnDelete();
            $table->foreignId('job_posting_id')->references('id')->on('job_postings')->restrictOnDelete();

            $table->unique(['job_seeker_id', 'job_posting_id', 'deleted_at']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_jobs');
    }
};

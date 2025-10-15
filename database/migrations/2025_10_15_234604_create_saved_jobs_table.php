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
            $table->foreignId('job_seeker_id')->constrained('job_seekers')->onDelete('cascade');
            $table->foreignId('job_id')->constrained('job_postings')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamp('saved_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
            
            // Unique constraint to prevent duplicate saves
            $table->unique(['job_seeker_id', 'job_id']);
            
            // Indexes for performance
            $table->index('job_seeker_id');
            $table->index('job_id');
            $table->index('saved_at');
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

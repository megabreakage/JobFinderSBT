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
        Schema::create('job_posting_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            
            // Skill Requirements
            $table->boolean('is_required')->default(false);
            $table->enum('importance_level', ['nice_to_have', 'preferred', 'required', 'critical'])->default('preferred');
            
            $table->timestamps();
            
            // Indexes
            $table->unique(['job_posting_id', 'skill_id']);
            $table->index('skill_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posting_skill');
    }
};

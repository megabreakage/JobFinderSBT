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
        Schema::create('user_company_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            
            // Role Information
            $table->enum('role_type', ['owner', 'admin', 'hr_manager', 'recruiter', 'member'])->default('member');
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            
            // Permissions
            $table->boolean('can_post_jobs')->default(false);
            $table->boolean('can_manage_applications')->default(false);
            $table->boolean('can_manage_team')->default(false);
            $table->boolean('can_manage_billing')->default(false);
            
            // Status
            $table->boolean('is_primary_contact')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->unique(['user_id', 'company_id']);
            $table->index('company_id');
            $table->index(['company_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_company_roles');
    }
};

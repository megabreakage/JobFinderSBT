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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->enum('role_type', ['owner', 'admin', 'hr_manager', 'recruiter', 'employee'])->default('employee');
            $table->string('job_title')->nullable();
            $table->text('permissions')->nullable();
            $table->boolean('is_primary_contact')->default(false);
            $table->boolean('can_post_jobs')->default(false);
            $table->boolean('can_manage_applications')->default(false);
            $table->boolean('can_manage_subscriptions')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('joined_at')->nullable();
            $table->unsignedBigInteger('invited_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('invited_by')->references('id')->on('users')->onDelete('set null');

            $table->unique(['user_id', 'company_id', 'deleted_at']);
            $table->index(['company_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
            $table->index('deleted_at');
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

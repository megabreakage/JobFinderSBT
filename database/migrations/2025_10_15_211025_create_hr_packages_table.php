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
        Schema::create('hr_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('tier', ['starter', 'economy', 'business', 'enterprise']);
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 8, 2);
            $table->decimal('price_yearly', 10, 2);
            $table->json('features');
            $table->integer('max_job_posts')->nullable();
            $table->integer('max_active_jobs')->nullable();
            $table->integer('max_users')->nullable();
            $table->boolean('api_access')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->boolean('custom_branding')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'sort_order']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_packages');
    }
};

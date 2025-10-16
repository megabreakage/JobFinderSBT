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
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('search_query');
            $table->json('filters')->nullable(); // industry, location, job_type, etc.
            $table->integer('results_count')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->string('session_id')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes for efficient querying and analytics
            $table->index('user_id');
            $table->index('search_query');
            $table->index('results_count');
            $table->index('created_at');
            $table->fullText('search_query');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};

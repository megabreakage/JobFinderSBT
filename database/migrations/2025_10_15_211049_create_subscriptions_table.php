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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('hr_package_id');
            $table->enum('billing_cycle', ['monthly', 'yearly']);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('KES');
            $table->enum('status', ['active', 'cancelled', 'expired', 'pending', 'suspended'])->default('pending');
            $table->date('starts_at');
            $table->date('expires_at');
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->boolean('auto_renewal')->default(true);
            $table->integer('jobs_posted_count')->default(0);
            $table->integer('active_jobs_count')->default(0);
            $table->json('usage_stats')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreignId('hr_package_id')->references('id')->on('hr_packages')->onDelete('cascade');

            $table->index(['company_id', 'status']);
            $table->index(['status', 'expires_at']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

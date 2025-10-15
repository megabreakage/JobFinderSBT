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
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('hr_package_id')->constrained('hr_packages')->onDelete('restrict');
            $table->enum('billing_period', ['monthly', 'yearly'])->default('monthly');
            $table->enum('status', [
                'trial',
                'active',
                'pending',
                'past_due',
                'cancelled',
                'expired'
            ])->default('pending');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->date('next_billing_date')->nullable();
            $table->integer('job_posts_used')->default(0);
            $table->integer('active_jobs_count')->default(0);
            $table->integer('users_count')->default(1);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('company_id');
            $table->index('hr_package_id');
            $table->index('status');
            $table->index('ends_at');
            $table->index('next_billing_date');
            $table->index(['company_id', 'status']);
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

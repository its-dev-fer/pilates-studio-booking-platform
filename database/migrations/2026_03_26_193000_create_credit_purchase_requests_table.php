<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('credit_package_id')->constrained('credit_packages')->cascadeOnDelete();
            $table->string('payment_method', 30); // transfer|cash
            $table->string('status', 30)->default('pending'); // pending|approved|rejected
            $table->foreignId('requested_tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->date('requested_date')->nullable();
            $table->time('requested_time_slot')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_purchase_requests');
    }
};

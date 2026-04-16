<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->string('payment_method', 50)->nullable()->after('check_in_status');
            $table->string('booking_origin', 50)->nullable()->after('payment_method');
            $table->foreignId('credit_purchase_request_id')->nullable()->after('booking_origin')->constrained('credit_purchase_requests')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('credit_purchase_request_id');
            $table->dropColumn(['payment_method', 'booking_origin']);
        });
    }
};

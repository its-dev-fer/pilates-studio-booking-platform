<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table): void {
            $table->string('transfer_bank_name', 120)->nullable()->after('transfer_account_number');
            $table->string('transfer_account_holder', 255)->nullable()->after('transfer_bank_name');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table): void {
            $table->dropColumn(['transfer_bank_name', 'transfer_account_holder']);
        });
    }
};

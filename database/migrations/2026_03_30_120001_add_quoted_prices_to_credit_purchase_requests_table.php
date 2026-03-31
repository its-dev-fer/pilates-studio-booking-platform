<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credit_purchase_requests', function (Blueprint $table) {
            $table->decimal('quoted_base_price', 10, 2)->nullable()->after('credit_package_id');
            $table->decimal('quoted_final_price', 10, 2)->nullable()->after('quoted_base_price');
        });
    }

    public function down(): void
    {
        Schema::table('credit_purchase_requests', function (Blueprint $table) {
            $table->dropColumn(['quoted_base_price', 'quoted_final_price']);
        });
    }
};

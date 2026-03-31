<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credit_packages', function (Blueprint $table) {
            $table->boolean('has_new_customer_price')->default(false)->after('price');
            $table->decimal('new_customer_price', 10, 2)->nullable()->after('has_new_customer_price');
        });
    }

    public function down(): void
    {
        Schema::table('credit_packages', function (Blueprint $table) {
            $table->dropColumn(['has_new_customer_price', 'new_customer_price']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credit_packages', function (Blueprint $table) {
            $table->boolean('is_one_time_purchase')->default(false)->after('price');
        });

        Schema::create('credit_package_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('credit_package_id')->constrained('credit_packages')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'credit_package_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_package_purchases');

        Schema::table('credit_packages', function (Blueprint $table) {
            $table->dropColumn('is_one_time_purchase');
        });
    }
};

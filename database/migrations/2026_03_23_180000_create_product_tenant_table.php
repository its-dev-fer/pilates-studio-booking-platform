<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_tenant', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_id', 'tenant_id']);
        });

        foreach (DB::table('products')->select('id', 'tenant_id')->cursor() as $row) {
            if (! $row->tenant_id) {
                continue;
            }

            DB::table('product_tenant')->insert([
                'product_id' => $row->id,
                'tenant_id' => $row->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_tenant');
    }
};

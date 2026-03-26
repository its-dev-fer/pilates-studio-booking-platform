<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Throwable;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'catalog_key')) {
                $table->string('catalog_key')->nullable()->after('sku');
                $table->index('catalog_key');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            try {
                $table->dropUnique(['sku']);
            } catch (Throwable) {
                // Índice ya eliminado o motor con otro nombre
            }
        });

        $hasTenantSkuUnique = collect(Schema::getIndexes('products'))
            ->contains(function (array $index): bool {
                if (! ($index['unique'] ?? false)) {
                    return false;
                }
                $cols = $index['columns'] ?? [];

                return count($cols) === 2
                    && in_array('tenant_id', $cols, true)
                    && in_array('sku', $cols, true);
            });

        if (! $hasTenantSkuUnique) {
            Schema::table('products', function (Blueprint $table) {
                $table->unique(['tenant_id', 'sku']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            try {
                $table->dropUnique(['tenant_id', 'sku']);
            } catch (Throwable) {
                //
            }
        });

        try {
            Schema::table('products', function (Blueprint $table) {
                $table->unique('sku');
            });
        } catch (Throwable) {
            //
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'catalog_key')) {
                try {
                    $table->dropIndex(['catalog_key']);
                } catch (Throwable) {
                    //
                }
                $table->dropColumn('catalog_key');
            }
        });
    }
};

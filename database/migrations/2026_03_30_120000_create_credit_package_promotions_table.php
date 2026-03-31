<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('credit_package_promotions')) {
            Schema::create('credit_package_promotions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('credit_package_id')->constrained('credit_packages')->cascadeOnDelete();
                $table->timestamp('starts_at');
                $table->timestamp('ends_at');
                $table->string('type', 20);
                $table->decimal('discount_percent', 5, 2)->nullable();
                $table->decimal('promotional_price', 10, 2)->nullable();
                $table->timestamps();

                $table->index(['credit_package_id', 'starts_at', 'ends_at'], 'cppromo_pkg_dates');
            });

            return;
        }

        if ($this->indexMissing('credit_package_promotions', 'cppromo_pkg_dates')) {
            Schema::table('credit_package_promotions', function (Blueprint $table) {
                $table->index(['credit_package_id', 'starts_at', 'ends_at'], 'cppromo_pkg_dates');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_package_promotions');
    }

    private function indexMissing(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $count = DB::selectOne(
            'SELECT COUNT(1) AS c FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $indexName]
        );

        return (int) ($count->c ?? 0) === 0;
    }
};

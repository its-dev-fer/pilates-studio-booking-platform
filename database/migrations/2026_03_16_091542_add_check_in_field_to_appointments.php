<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('check_in_status', [
                'pendiente',
                'cliente_llego',
                'cliente_no_llego',
                'cancelada_por_cliente',
                'cancelada_por_administrador',
                'cancelada_por_empleado',
                'cobrar_al_llegar'
            ])->default('pendiente')->after('status');
            $table->foreignId('checked_in_by')->nullable()->references('id')->on('users')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('check_in_status');
            $table->dropForeign(['checked_in_by']);
        });
    }
};

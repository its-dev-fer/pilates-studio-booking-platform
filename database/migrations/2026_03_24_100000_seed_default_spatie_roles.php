<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $guard = 'web';

        foreach (['admin', 'empleado', 'cliente'] as $name) {
            Role::firstOrCreate(
                ['name' => $name, 'guard_name' => $guard],
            );
        }
    }

    public function down(): void
    {
        // No borramos roles en rollback: podrían tener usuarios asignados.
    }
};

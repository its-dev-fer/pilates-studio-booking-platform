<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Roles usados en la app (Spatie, guard web).
     */
    public function run(): void
    {
        $guard = 'web';

        foreach (['admin', 'empleado', 'cliente'] as $name) {
            Role::firstOrCreate(
                ['name' => $name, 'guard_name' => $guard],
            );
        }
    }
}

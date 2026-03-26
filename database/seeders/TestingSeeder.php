<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(RolesSeeder::class);

        // 1. Rol administrador (ya creado por RolesSeeder)
        $adminRole = Role::findByName('admin', 'web');

        // 2. Crear una Sucursal (Tenant) Inicial
        // Aunque el admin es omnipresente, Filament Multitenancy requiere
        // que exista al menos un Tenant en el sistema para poder navegar.
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'tuxtla-matriz'],
            [
                'name' => 'Tuxtla Matriz',
                'address' => 'Av. Principal 123, Ciudad',
                'max_appointments_per_day' => 5,
                'shipping_fee' => 100.00,
            ]
        );

        // 3. Crear el Usuario Administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super',
                'last_name' => 'Administrador',
                'password' => Hash::make('password'),
                'phone' => '9611003141',
            ]
        );

        // 4. Asignarle el Rol de Admin al Usuario
        $admin->assignRole($adminRole);

        // Opcional: Aunque el admin es omnipresente, lo vinculamos a la sucursal
        // para que no haya problemas de relación vacía al probar ciertas vistas.
        $admin->tenants()->syncWithoutDetaching([$tenant->id]);

        $this->command->info('¡Entorno base creado! Roles, Sucursal y Admin listos.');
    }
}

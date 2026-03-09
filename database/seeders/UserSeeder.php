<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset permission cache
        app()['cache']->forget('spatie.permission.cache');

        $permissions = [
            'modulo resoluciones',
            'modulo cargos',
            'modulo usuarios',
            'modulo roles',
            'modulo configuracion',
            'resolucion ingresar',
            'resolucion exportar',
            'resolucion importar excel',
            'resolucion ver indicadores',
            'charges.view',
            'charges.create',
            'charges.edit',
            'charges.sign',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $roles = [
            'ADMINISTRADOR' => [
                'modulo resoluciones',
                'modulo cargos',
                'modulo usuarios',
                'modulo roles',
                'modulo configuracion',
                'resolucion ingresar',
                'resolucion exportar',
                'resolucion importar excel',
                'resolucion ver indicadores',
                'charges.view',
                'charges.create',
                'charges.edit',
                'charges.sign',
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
            ],
            'REGISTRADOR RESOLUCIONES' => [
                'modulo resoluciones',
                'resolucion ingresar',
                'resolucion exportar',
                'resolucion importar excel',
                'resolucion ver indicadores',
                'modulo cargos',
            ],
            'REGISTRADOR' => [
                'modulo resoluciones',
                'modulo cargos',
            ],
            'LECTOR' => [
                'modulo resoluciones',
                'modulo cargos',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['created_at' => now(), 'updated_at' => now()]
            );
            $role->syncPermissions($rolePermissions);
        }

        $user = User::create([
            'name' => 'ADMIN',
            'last_name' => 'ADMIN',
            'dni' => '000000000',
            'password' => Hash::make('123456'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user->assignRole('ADMINISTRADOR');

        $user2 = User::create([
            'name' => 'REGISTRADOR',
            'last_name' => 'USER',
            'dni' => '111111111',
            'password' => Hash::make('123456'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user2->assignRole('REGISTRADOR');
    }
}

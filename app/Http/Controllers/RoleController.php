<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        foreach ($this->modulePermissions() as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $roles = Role::with('permissions')->get();
        $permissions = Permission::orderBy('name')->get();

        $permissionLabels = $this->permissionLabels();
        $permissionGroups = $this->permissionGroups();

        return view('roles.index', compact('roles', 'permissions', 'permissionLabels', 'permissionGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        foreach ($this->modulePermissions() as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $allPermissions = Permission::pluck('name')->all();
        $isAdmin = strtoupper($data['name']) === 'ADMINISTRADOR';
        $role->syncPermissions($isAdmin ? $allPermissions : ($data['permissions'] ?? []));

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        foreach ($this->modulePermissions() as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        if (strtoupper($role->name) === 'ADMINISTRADOR') {
            $data['name'] = 'ADMINISTRADOR';
        }

        $role->update(['name' => $data['name']]);

        $allPermissions = Permission::pluck('name')->all();
        $isAdmin = strtoupper($data['name']) === 'ADMINISTRADOR';
        $role->syncPermissions($isAdmin ? $allPermissions : ($data['permissions'] ?? []));

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente');
    }

    protected function modulePermissions(): array
    {
        $permissions = [];
        foreach ($this->permissionGroups() as $group) {
            $permissions[] = $group['module'];
            foreach ($group['permissions'] as $permission) {
                $permissions[] = $permission;
            }
        }

        return array_values(array_unique($permissions));
    }

    protected function permissionGroups(): array
    {
        return [
            [
                'module' => 'modulo resoluciones',
                'permissions' => [
                    'resolucion exportar',
                    'resolucion ingresar',
                    'resolucion ver indicadores',
                    'resolucion importar excel',
                ],
            ],
            [
                'module' => 'modulo cargos',
                'permissions' => [
                    'charges.view',
                    'charges.create',
                    'charges.edit',
                    'charges.sign',
                ],
            ],
            [
                'module' => 'modulo usuarios',
                'permissions' => [
                    'users.view',
                    'users.create',
                    'users.edit',
                    'users.delete',
                ],
            ],
            [
                'module' => 'modulo personas naturales',
                'permissions' => [
                    'natural-people.view',
                    'natural-people.create',
                    'natural-people.edit',
                    'natural-people.delete',
                ],
            ],
            [
                'module' => 'modulo roles',
                'permissions' => [],
            ],
            [
                'module' => 'modulo personas juridicas',
                'permissions' => [
                    'legal-entities.view',
                    'legal-entities.create',
                    'legal-entities.edit',
                    'legal-entities.delete',
                ],
            ],
            [
                'module' => 'modulo configuracion',
                'permissions' => [],
            ],
            [
                'module' => 'modulo registro de actividades',
                'permissions' => [
                    'activity_logs.view',
                ],
            ],
        ];
    }

    protected function permissionLabels(): array
    {
        return [
            'modulo resoluciones' => 'Acceso al módulo de resoluciones',
            'modulo cargos' => 'Acceso al módulo de cargos',
            'modulo usuarios' => 'Acceso al módulo de usuarios',
            'modulo personas naturales' => 'Acceso al modulo de personas naturales',
            'modulo roles' => 'Acceso al módulo de roles',
            'modulo personas juridicas' => 'Acceso al modulo de personas juridicas',
            'modulo configuracion' => 'Acceso al módulo de configuración',
            'modulo registro de actividades' => 'Acceso al módulo de registro de actividades',
            'resolucion ingresar' => 'Registrar resoluciones',
            'resolucion exportar' => 'Exportar resoluciones',
            'resolucion importar excel' => 'Importar resoluciones desde Excel',
            'resolucion ver indicadores' => 'Ver indicadores de resoluciones',
            'resolucions.view' => 'Ver resoluciones',
            'resolucions.create' => 'Crear resoluciones',
            'resolucions.edit' => 'Editar resoluciones',
            'resolucions.delete' => 'Eliminar resoluciones',
            'charges.view' => 'Ver cargos',
            'charges.create' => 'Crear cargos',
            'charges.edit' => 'Editar cargos',
            'charges.sign' => 'Firmar cargos',
            'users.view' => 'Ver usuarios',
            'users.create' => 'Crear usuarios',
            'users.edit' => 'Editar usuarios',
            'users.delete' => 'Eliminar usuarios',
            'natural-people.view' => 'Ver personas naturales',
            'natural-people.create' => 'Crear personas naturales',
            'natural-people.edit' => 'Editar personas naturales',
            'natural-people.delete' => 'Eliminar personas naturales',
            'legal-entities.view' => 'Ver personas juridicas',
            'legal-entities.create' => 'Crear personas juridicas',
            'legal-entities.edit' => 'Editar personas juridicas',
            'legal-entities.delete' => 'Eliminar personas juridicas',
            'activity_logs.view' => 'Ver registro de actividades',
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente');
    }
}

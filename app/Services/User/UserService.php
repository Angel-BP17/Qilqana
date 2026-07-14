<?php

namespace App\Services\User;

use App\Models\NaturalPerson;
use App\Models\User;
use App\Services\User\Contracts\UserServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService implements UserServiceInterface
{
    public function getAll(array $data): array
    {
        $users = User::with('roles')
            ->search($data['search'] ?? null)
            ->filterByRole($data['role_id'] ?? null)
            ->paginate(10);

        $roles = Role::all();

        return compact('users', 'roles');
    }

    public function create(array $data): bool
    {
        try {
            return DB::transaction(function () use ($data) {
                // 1. Gestionar Persona Natural
                $this->syncNaturalPerson($data);

                // 2. Preparar datos de Usuario
                $data['password'] = Hash::make($data['password']);
                $data['last_name'] = mb_strtoupper(trim(($data['apellido_paterno'] ?? '').' '.($data['apellido_materno'] ?? '')), 'UTF-8');
                $data['name'] = mb_strtoupper($data['name'], 'UTF-8');

                $user = User::create($data);

                if (! empty($data['roles'])) {
                    $user->assignRole($data['roles']);
                }

                return true;
            });
        } catch (\Throwable $th) {
            report($th);

            return false;
        }
    }

    public function update(array $data, int $id): bool
    {
        try {
            return DB::transaction(function () use ($data, $id) {
                $model = User::findOrFail($id);

                // 1. Gestionar Persona Natural
                $this->syncNaturalPerson($data);

                // 2. Actualizar Usuario
                if (! empty($data['password'])) {
                    $data['password'] = Hash::make($data['password']);
                } else {
                    unset($data['password']);
                }

                if (isset($data['apellido_paterno']) || isset($data['apellido_materno'])) {
                    $data['last_name'] = mb_strtoupper(trim(($data['apellido_paterno'] ?? '').' '.($data['apellido_materno'] ?? '')), 'UTF-8');
                }

                if (isset($data['name'])) {
                    $data['name'] = mb_strtoupper($data['name'], 'UTF-8');
                }

                $roles = $data['roles'] ?? null;
                $hasRoles = array_key_exists('roles', $data);
                unset($data['roles']);

                $model->update($data);
                if ($hasRoles) {
                    $model->syncRoles($roles ?? []);
                }

                return true;
            });
        } catch (\Throwable $th) {
            report($th);

            return false;
        }
    }

    protected function syncNaturalPerson(array $data): void
    {
        if (! empty($data['dni'])) {
            NaturalPerson::updateOrCreate(
                ['dni' => $data['dni']],
                [
                    'nombres' => mb_strtoupper($data['name'] ?? '', 'UTF-8'),
                    'apellido_paterno' => mb_strtoupper($data['apellido_paterno'] ?? '', 'UTF-8'),
                    'apellido_materno' => mb_strtoupper($data['apellido_materno'] ?? '', 'UTF-8'),
                ]
            );
        }
    }

    public function delete(array $data, int $id): bool
    {
        try {
            $model = User::findOrFail($id);

            return (bool) $model->delete();
        } catch (\Throwable $th) {
            report($th);

            return false;
        }
    }
}

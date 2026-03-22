<?php

namespace App\Services;

use App\Filters\UserFilter;
use App\Models\User;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService implements UserServiceInterface
{
    public function __construct(protected UserFilter $filter)
    {
    }

    public function getAll(array $data): array
    {
        $users = $this->filter->applyFilters($data)->paginate(10);
        $roles = Role::all();
        return compact('users', 'roles');
    }

    public function create(array $data): bool
    {
        try {
            return DB::transaction(function () use ($data) {
                $data['password'] = Hash::make($data['password']);
                $user = User::create($data);
                
                if (!empty($data['roles'])) $user->assignRole($data['roles']);
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

                if (!empty($data['password'])) {
                    $data['password'] = Hash::make($data['password']);
                } else {
                    unset($data['password']);
                }

                $roles = $data['roles'] ?? null;
                $hasRoles = array_key_exists('roles', $data);
                unset($data['roles']);

                $model->update($data);
                if ($hasRoles) $model->syncRoles($roles ?? []);
                
                return true;
            });
        } catch (\Throwable $th) {
            report($th);
            return false;
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

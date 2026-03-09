<?php
namespace App\Services;

use App\Filters\UserFilter;
use App\Models\User;
use App\Services\Contracts\UserServiceInterface;
use DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService implements UserServiceInterface
{
    public function __construct(protected UserFilter $filter)
    {
    }
    public function getAll(array $data): array
    {
        $users = $this->filter->applyFilters($data)
            ->paginate(10);

        $roles = Role::all();

        return compact('users', 'roles');
    }

    public function find(array $criteria): array
    {
        throw new \Exception('Not implemented');
    }

    public function getById(int $id): array
    {
        throw new \Exception('Not implemented');
    }

    public function create(array $data): bool
    {
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        if (!empty($data['roles'])) {
            $user->assignRole($data['roles']);
        }

        return true;
    }

    public function update(array $data, int $id): bool
    {
        try {
            DB::transaction(function () use ($data, $id) {
                $model = User::findOrFail($id);

                // Evitar sobreescritura con null cuando no se envia contrasena.
                if (!empty($data['password'])) {
                    $data['password'] = Hash::make($data['password']);
                } else {
                    unset($data['password']);
                }

                $hasRoles = array_key_exists('roles', $data);
                $roles = $data['roles'] ?? null;
                unset($data['roles']);

                $model->update($data);

                if ($hasRoles) {
                    $model->syncRoles($roles ?? []);
                }
            });
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
        return true;
    }

    public function delete(array $data, $id): bool
    {
        $model = User::findOrFail($id);
        $model->delete();

        return true;
    }
}

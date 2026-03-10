<?php

namespace App\Services;

use App\Filters\UserFilter;
use App\Models\User;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService implements UserServiceInterface
{
    protected const CACHE_TTL = 3600;

    public function __construct(protected UserFilter $filter)
    {
    }

    public function getAll(array $data): array
    {
        $version = Cache::get('users_cache_version', 1);
        $cacheKey = "users_v{$version}_index_" . md5(serialize($data));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($data) {
            $users = $this->filter->applyFilters($data)->paginate(10);
            $roles = Role::all();
            return compact('users', 'roles');
        });
    }

    public function create(array $data): bool
    {
        try {
            $result = DB::transaction(function () use ($data) {
                $data['password'] = Hash::make($data['password']);
                $user = User::create($data);
                
                if (!empty($data['roles'])) $user->assignRole($data['roles']);
                return true;
            });

            if ($result) $this->clearCache();
            return $result;
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    public function update(array $data, int $id): bool
    {
        try {
            $result = DB::transaction(function () use ($data, $id) {
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

            if ($result) $this->clearCache();
            return $result;
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    public function delete(array $data, int $id): bool
    {
        try {
            $model = User::findOrFail($id);
            $deleted = (bool) $model->delete();
            if ($deleted) $this->clearCache();
            return $deleted;
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    private function clearCache(): void
    {
        Cache::increment('users_cache_version');
        Cache::forget('users_to_assign_0');
        // También invalidamos la caché de cargos porque los nombres de usuarios pueden haber cambiado
        Cache::increment('charges_cache_version');
    }
}

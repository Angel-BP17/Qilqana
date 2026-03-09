<?php
namespace App\Filters;

use App\Models\User;

class UserFilter
{
    public function applyFilters($data)
    {
        $search = $data['search'] ?? null;
        $roleId = $data['role_id'] ?? null;

        return User::with('roles')
            ->when(
                $search,
                fn($q, $search) => $q->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('dni', 'LIKE', "%{$search}%")
                        ->orWhereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("CONCAT(last_name, ' ', name) LIKE ?", ["%{$search}%"]);
                })
            )
            ->when($roleId, fn($q, $roleId) => $q->whereHas('roles', fn($q) => $q->where('id', $roleId)));
    }
}

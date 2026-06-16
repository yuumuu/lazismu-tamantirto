<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function list(): Collection
    {
        return User::latest()->get();
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role' => $data['role'] ?? null,
            ]);
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => isset($data['password']) ? bcrypt($data['password']) : $user->password,
                'role' => $data['role'] ?? $user->role,
                'photo' => $data['photo'] ?? $user->photo,
                'is_active' => $data['is_active'] ?? $user->is_active,
            ]);

            return $user;
        });
    }

    public function setActiveStatus(User $user, bool $isActive): User
    {
        $user->is_active = $isActive;
        $user->save();

        return $user;
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function updateLoginInfo(User $user, string $ipAddress): void
    {
        $user->last_login_at = now();
        $user->last_login_ip = $ipAddress;
        $user->save();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}

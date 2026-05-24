<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * List all users.
     */
    public function list(): Collection
    {
        return User::with('roles')->latest()->get();
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

            if (isset($data['role'])) {
                $user->assignRole($data['role']);
            }

            return $user;
        });
    }

    /**
     * Update an existing user.
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => isset($data['password']) ? bcrypt($data['password']) : $user->password,
                'photo' => $data['photo'] ?? $user->photo,
                'is_active' => $data['is_active'] ?? $user->is_active,
            ]);

            if (isset($data['role'])) {
                $user->syncRoles([$data['role']]);
            }

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
        return User::with('roles')->find($id);
    }

    public function updateLoginInfo(User $user, string $ipAddress): void
    {
        $user->last_login_at = now();
        $user->last_login_ip = $ipAddress;
        $user->save();
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): void
    {
        $user->delete();
    }
}

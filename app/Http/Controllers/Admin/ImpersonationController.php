<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function impersonate(int $id): RedirectResponse
    {
        $user = User::withoutGlobalScope('branch')->findOrFail($id);

        if (! Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        if (Auth::id() === $user->id) {
            return redirect()->route('dashboard');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('dashboard')
                ->with('notify', ['message' => 'Tidak dapat impersonate Super Admin lain.', 'type' => 'error']);
        }

        if ($user->role !== 'admin') {
            return redirect()->route('dashboard')
                ->with('notify', ['message' => 'Hanya akun admin cabang yang bisa di-impersonate.', 'type' => 'error']);
        }

        if (! $user->branch_id) {
            return redirect()->route('dashboard')
                ->with('notify', ['message' => 'Akun ini tidak memiliki cabang.', 'type' => 'error']);
        }

        $branch = Branch::find($user->branch_id);
        if (! $branch || ! $branch->is_active) {
            return redirect()->route('dashboard')
                ->with('notify', ['message' => 'Cabang tidak aktif atau tidak ditemukan.', 'type' => 'error']);
        }

        session(['impersonated_by' => Auth::id()]);
        session(['active_branch_id' => $user->branch_id]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('notify', ['message' => "Anda sekarang masuk sebagai {$user->name} ({$branch->name})", 'type' => 'success']);
    }

    public function leave(): RedirectResponse
    {
        if (! session()->has('impersonated_by')) {
            return redirect()->route('dashboard');
        }

        $originalUserId = session()->pull('impersonated_by');
        $originalUser = User::withoutGlobalScope('branch')->findOrFail($originalUserId);

        Auth::login($originalUser);

        session(['active_branch_id' => $originalUser->branch_id ?? 1]);

        return redirect()->route('admin.branches.index')
            ->with('notify', ['message' => 'Kembali ke akun Super Admin', 'type' => 'success']);
    }
}

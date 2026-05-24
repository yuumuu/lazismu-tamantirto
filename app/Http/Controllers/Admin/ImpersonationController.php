<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a user.
     */
    public function impersonate(int $id): RedirectResponse
    {
        $user = User::withoutGlobalScope('branch')->findOrFail($id);

        // Only SuperAdmin can impersonate
        if (! Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        // Prevent self-impersonation
        if (Auth::id() === $user->id) {
            return redirect()->route('dashboard');
        }

        // Store original user ID in session
        session(['impersonated_by' => Auth::id()]);

        // Login as target user
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('notify', ['message' => "Anda sekarang masuk sebagai {$user->name}", 'type' => 'success']);
    }

    /**
     * Stop impersonating and return to original account.
     */
    public function leave(): RedirectResponse
    {
        if (! session()->has('impersonated_by')) {
            return redirect()->route('dashboard');
        }

        $originalUserId = session()->pull('impersonated_by');
        $originalUser = User::withoutGlobalScope('branch')->findOrFail($originalUserId);

        Auth::login($originalUser);

        return redirect()->route('admin.branchs.index')
            ->with('notify', ['message' => 'Kembali ke akun Super Admin', 'type' => 'success']);
    }
}

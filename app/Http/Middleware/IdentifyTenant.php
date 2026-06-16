<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $activeBranchId = null;

        // 1. Priority: URL Slug (Public context)
        $slug = $request->route('branch_slug');

        // Set URL default to preserve the current branch context in generated links
        URL::defaults(['branch_slug' => $slug]);

        if ($slug) {
            $branch = Branch::where('slug', $slug)->first();
            if ($branch) {
                $activeBranchId = $branch->id;
            }
        }

        // 2. Secondary: Authenticated User (Admin context)
        if (! $activeBranchId && Auth::check()) {
            $activeBranchId = Auth::user()->branch_id;
        }

        // 3. Fallback: Pusat (ID 1)
        if (! $activeBranchId) {
            $activeBranchId = 1;
        }

        session(['active_branch_id' => $activeBranchId]);

        // 4. Security: Cross-tenant access protection
        if (Auth::check() && ! Auth::user()->isSuperAdmin() && Auth::user()->branch_id !== null) {
            if ((int) $activeBranchId !== (int) Auth::user()->branch_id) {
                // If trying to access admin area of another branch, block it.
                if ($request->is('admin*') || $request->is('manage*')) {
                    abort(403, 'Anda tidak memiliki akses ke cabang ini.');
                }
            }
        }

        return $next($request);
    }
}

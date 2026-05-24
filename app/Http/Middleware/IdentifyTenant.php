<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Masjid;
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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $activeMasjidId = null;

        // 1. Priority: URL Slug (Public context)
        $slug = $request->route('masjid_slug');

        // Set URL default to preserve the current masjid context in generated links
        URL::defaults(['masjid_slug' => $slug]);

        if ($slug) {
            $masjid = Masjid::where('slug', $slug)->first();
            if ($masjid) {
                $activeMasjidId = $masjid->id;
            }
        }

        // 2. Secondary: Authenticated User (Admin context)
        if (! $activeMasjidId && Auth::check()) {
            $activeMasjidId = Auth::user()->masjid_id;
        }

        // 3. Fallback: Pusat (ID 1)
        if (! $activeMasjidId) {
            $activeMasjidId = 1;
        }

        session(['active_masjid_id' => $activeMasjidId]);

        // 4. Security: Cross-tenant access protection
        if (Auth::check() && ! Auth::user()->isSuperAdmin()) {
            if ((int) $activeMasjidId !== (int) Auth::user()->masjid_id) {
                // If trying to access admin area of another masjid, block it.
                if ($request->is('admin*') || $request->is('manage*')) {
                    abort(403, 'Anda tidak memiliki akses ke cabang ini.');
                }
            }
        }

        return $next($request);
    }
}

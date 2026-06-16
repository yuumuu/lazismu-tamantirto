<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'tenant' => [
                'branch_id' => session('active_branch_id', 1),
                'branch_slug' => $request->route('branch_slug'),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'site' => [
                'name' => setting('site_name', 'Lazismu'),
                'tagline' => setting('site_tagline', 'Tamantirto'),
                'description' => setting('site_description', 'Memberi untuk negeri.'),
                'address' => setting('contact_address', ''),
                'phone' => setting('contact_phone', ''),
                'email' => setting('contact_email', ''),
                'whatsapp' => setting('contact_whatsapp', ''),
                'footer_text' => setting('footer_text', '© '.date('Y').' Lazismu Tamantirto. All rights reserved.'),
                'qris' => setting('site_qris'),
                'social' => [
                    'facebook' => setting('social_facebook'),
                    'instagram' => setting('social_instagram'),
                    'twitter' => setting('social_twitter'),
                    'youtube' => setting('social_youtube'),
                ],
                'pages' => Cache::remember('published_pages', 3600, fn () => Page::query()->where('status', 'published')->get(['title', 'slug'])
                ),
            ],
        ]);
    }
}

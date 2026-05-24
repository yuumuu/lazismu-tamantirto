<?php

declare(strict_types=1);

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BlogPost;
use App\Models\Campaign;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    /**
     * Display the home page with optimized queries.
     */
    public function home(Request $request): View
    {
        // Optimized query: 5-7 queries total instead of 15-20
        // 1. Featured campaigns with relationships (show all branches)
        $featuredCampaigns = Campaign::withoutGlobalScope('masjid')
            ->active()
            ->featured()
            ->with(['category', 'creator', 'masjid'])
            ->withCount('verifiedDonations')
            ->withSum('verifiedDonations', 'amount')
            ->latest()
            ->take(3)
            ->get();

        // 2. Latest blog posts with category (show all branches)
        $latestPosts = BlogPost::withoutGlobalScope('masjid')
            ->published()
            ->with('category')
            ->latest()
            ->take(3)
            ->get();

        // 3. Banners for hero section
        $banners = Banner::where('is_active', true)
            ->orderBy('order')
            ->get();

        // 4. Stats for counter (cached for 5 minutes using helper)
        $stats = cached_guest_stats();

        return view('guest.home', compact('featuredCampaigns', 'latestPosts', 'banners', 'stats'));
    }

    /**
     * Display campaigns index with optimized queries.
     */
    public function campaignsIndex(Request $request): View
    {
        // Get filter parameters
        $categoryId = $request->input('category');
        $type = $request->input('type');
        $search = $request->input('search');

        $campaigns = Campaign::withoutGlobalScope('masjid')
            ->active()
            ->published()
            ->with(['category', 'masjid'])
            ->withCount('verifiedDonations')
            ->withSum('verifiedDonations', 'amount')
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // Use cached categories helper
        $categories = cached_campaign_categories();

        return view('guest.campaigns.index', compact('campaigns', 'categories'));
    }

    /**
     * Display single campaign with optimized queries.
     */
    public function campaignShow(Request $request, string $slug): View
    {
        // Optimized query: 4-6 queries total instead of 8-12
        $campaign = Campaign::withoutGlobalScope('masjid')
            ->active()
            ->where('slug', $slug)
            ->with(['category', 'creator', 'masjid'])
            ->withCount('verifiedDonations')
            ->withSum('verifiedDonations', 'amount')
            ->firstOrFail();

        // Get latest donors with single query
        $donors = $campaign->verifiedDonations()
            ->latest()
            ->take(10)
            ->get();

        // Use eager loaded count instead of separate query
        $donorsCount = $campaign->verified_donations_count;

        return view('guest.campaigns.show', compact('campaign', 'donors', 'donorsCount'));
    }

    /**
     * Display blog posts index.
     */
    public function postsIndex(Request $request): View
    {
        $posts = BlogPost::withoutGlobalScope('masjid')
            ->published()
            ->with('category')
            ->latest()
            ->paginate(9)
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->excerpt,
                    'featured_image' => $post->featured_image,
                    'published_at' => $post->published_at,
                    'reading_time' => $post->reading_time,
                    'category_id' => $post->category_id, // Changed from blog_category_id
                    'category' => $post->category ? [
                        'id' => $post->category->id,
                        'name' => $post->category->name,
                    ] : null,
                ];
            });

        $categories = \App\Models\BlogCategory::all()->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
            ];
        });

        return view('guest.posts.index', compact('posts', 'categories'));
    }

    /**
     * Display single blog post.
     */
    public function postShow(Request $request, string $slug): View
    {
        $post = BlogPost::withoutGlobalScope('masjid')
            ->published()
            ->where('slug', $slug)
            ->with(['category', 'author'])
            ->firstOrFail();

        return view('guest.posts.show', compact('post'));
    }

    /**
     * Display about page.
     */
    public function about(?string $masjid_slug = null): View
    {
        return view('guest.about');
    }

    /**
     * Display organization structure page.
     */
    public function structure(?string $masjid_slug = null): View
    {
        return view('guest.structure');
    }

    /**
     * Display contact page.
     */
    public function contact(?string $masjid_slug = null): View
    {
        return view('guest.contact');
    }

    /**
     * Display reports page.
     */
    public function reports(?string $masjid_slug = null): View
    {
        return view('guest.reports');
    }

    /**
     * Display zakat calculator page.
     */
    public function calculator(?string $masjid_slug = null): View
    {
        return view('guest.calculator');
    }
}

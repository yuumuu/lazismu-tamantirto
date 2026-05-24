<?php

declare(strict_types=1);

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BlogPost;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuestController extends Controller
{
    /**
     * Display the home page with optimized queries.
     */
    public function home(Request $request): Response
    {
        // Optimized query: 5-7 queries total instead of 15-20
        // 1. Featured campaigns with relationships (show all branches)
        $featuredCampaigns = Campaign::withoutGlobalScope('branch')
            ->active()
            ->featured()
            ->with(['category', 'creator', 'branch'])
            ->withCount('verifiedDonations')
            ->withSum('verifiedDonations', 'amount')
            ->latest()
            ->take(3)
            ->get();

        // 2. Latest blog posts with category (show all branches)
        $latestPosts = BlogPost::withoutGlobalScope('branch')
            ->published()
            ->with('category')
            ->latest()
            ->take(3)
            ->get();

        // 3. Banners for hero section
        $banners = Banner::where('is_active', true)
            ->orderBy('order', 'DESC')
            ->get();

        // 4. Stats for counter (cached for 5 minutes using helper)
        $stats = cached_guest_stats();

        return Inertia::render('Welcome', [
            'featuredCampaigns' => $featuredCampaigns,
            'latestPosts' => $latestPosts,
            'banners' => $banners,
            'stats' => $stats,
        ]);
    }

    /**
     * Display campaigns index with optimized queries.
     */
    public function campaignsIndex(Request $request)
    {
        // Get filter parameters
        $categoryId = $request->input('category');
        $type = $request->input('type');
        $search = $request->input('search');

        $campaigns = Campaign::withoutGlobalScope('branch')
            ->active()
            ->published()
            ->with(['category', 'branch'])
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

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns,
            'categories' => $categories,
            'filters' => request()->only(['category', 'type', 'search'])
        ]);
    }

    /**
     * Display single campaign with optimized queries.
     */
    public function campaignShow(Request $request, string $slug)
    {
        // Optimized query: 4-6 queries total instead of 8-12
        $campaign = Campaign::withoutGlobalScope('branch')
            ->active()
            ->where('slug', $slug)
            ->with(['category', 'creator', 'branch'])
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

        // Fetch related blog posts (berita penyaluran donasi)
        $relatedPosts = \App\Models\BlogPost::withoutGlobalScope('branch')
            ->published()
            ->where('campaign_id', $campaign->id)
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Campaigns/Show', [
            'campaign' => $campaign,
            'donors' => $donors,
            'donorsCount' => $donorsCount,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    /**
     * Display blog posts index.
     */
    public function postsIndex(Request $request)
    {
        $posts = BlogPost::withoutGlobalScope('branch')
            ->published()
            ->with('category')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $categories = \App\Models\BlogCategory::all()->map(fn ($cat) => [
            'id' => $cat->id,
            'name' => $cat->name,
        ]);

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
            'categories' => $categories,
            'filters' => request()->only(['category', 'search']),
        ]);
    }

    /**
     * Display single blog post.
     */
    public function postShow(Request $request, string $slug)
    {
        $post = BlogPost::withoutGlobalScope('branch')
            ->published()
            ->where('slug', $slug)
            ->with(['category', 'author'])
            ->firstOrFail();

        return Inertia::render('Posts/Show', [
            'post' => $post,
        ]);
    }

    /**
     * Display about page.
     */
    public function about(?string $branch_slug = null)
    {
        return view('guest.about');
    }

    /**
     * Display organization structure page.
     */
    public function structure(?string $branch_slug = null)
    {
        return view('guest.structure');
    }

    /**
     * Display contact page.
     */
    public function contact(?string $branch_slug = null)
    {
        return view('guest.contact');
    }

    /**
     * Display reports page.
     */
    public function reports(?string $branch_slug = null)
    {
        return view('guest.reports');
    }

    /**
     * Display zakat calculator page.
     */
    public function calculator(?string $branch_slug = null)
    {
        return view('guest.calculator');
    }
}

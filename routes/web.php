<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Guest\DonateController;
use App\Http\Controllers\Guest\GuestController;
use App\Http\Controllers\Guest\NeedController;
use App\Models\Page;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

// Impersonation Leave (Global to avoid domain/prefix issues)
Route::post('/admin/impersonate/leave', [ImpersonationController::class, 'leave'])->name('admin.impersonate.leave');

// Favicon route with proper cache headers
Route::get('/favicon.{ext}', function ($ext) {
    $path = public_path("favicon.{$ext}");

    if (! file_exists($path)) {
        abort(404);
    }

    $mimeTypes = [
        'ico' => 'image/x-icon',
        'png' => 'image/png',
        'svg' => 'image/svg+xml',
    ];

    return response()->file($path, [
        'Content-Type' => $mimeTypes[$ext] ?? 'application/octet-stream',
        'Cache-Control' => 'public, max-age=86400', // 1 day cache
        'Expires' => now()->addDay()->toRfc7231String(),
    ]);
})->where('ext', 'ico|png|svg');

// Domain configuration
$appDomain = app()->runningUnitTests() ? 'localhost' : env('APP_DOMAIN', 'lazismu.test');
$adminDomain = app()->runningUnitTests() ? 'localhost' : env('ADMIN_DOMAIN', 'admin.lazismu.test');
$sameDomain = $appDomain === $adminDomain;

// Admin Routes
Route::domain($adminDomain)->group(function () use ($sameDomain) {
    Route::middleware(['auth', 'verified', 'role:super_admin|admin|editor|viewer'])
        ->prefix($sameDomain ? 'admin' : '')
        ->group(function () {
            Volt::route('/dashboard', 'admin.dashboard')->name('dashboard');

            // Campaign Management
            Route::prefix('campaigns')->name('admin.campaigns.')->group(function () {
                Volt::route('/', 'admin.campaigns.index')->name('index');
                Volt::route('/create', 'admin.campaigns.create')->name('create');
                Volt::route('/{campaign}/edit', 'admin.campaigns.edit')->name('edit');
            });

            // Donation Management
            Route::prefix('donations')->name('admin.donations.')->group(function () {
                Volt::route('/', 'admin.donations.index')->name('index');
                Volt::route('/create', 'admin.donations.create')->name('create');
                Volt::route('/{donation}', 'admin.donations.show')->name('show');
            });

            // Withdrawal Management
            Route::prefix('withdrawals')->name('admin.withdrawals.')->group(function () {
                Volt::route('/', 'admin.withdrawals.index')->name('index');
                Volt::route('/create', 'admin.withdrawals.create')->name('create');
                Volt::route('/{withdrawal}', 'admin.withdrawals.show')->name('show');
                Volt::route('/{withdrawal}/edit', 'admin.withdrawals.edit')->name('edit');
            });

            // Master Data & Content
            Route::prefix('manage')->name('admin.')->group(function () {
                Volt::route('/muzakkis', 'admin.muzakkis.index')->name('muzakkis.index');
                Volt::route('/muzakkis/create', 'admin.muzakkis.create')->name('muzakkis.create');
                Volt::route('/muzakkis/{muzakki}/edit', 'admin.muzakkis.edit')->name('muzakkis.edit');

                Volt::route('/mustahiks', 'admin.mustahiks.index')->name('mustahiks.index');
                Volt::route('/mustahiks/create', 'admin.mustahiks.create')->name('mustahiks.create');
                Volt::route('/mustahiks/{mustahik}/edit', 'admin.mustahiks.edit')->name('mustahiks.edit');

                Volt::route('/distributors', 'admin.distributors.index')->name('distributors.index');
                Volt::route('/distributors/create', 'admin.distributors.create')->name('distributors.create');
                Volt::route('/distributors/{distributor}/edit', 'admin.distributors.edit')->name('distributors.edit');

                Volt::route('/pages', 'admin.pages.index')->name('pages.index');
                Volt::route('/pages/create', 'admin.pages.create')->name('pages.create');
                Volt::route('/pages/{page}/edit', 'admin.pages.edit')->name('pages.edit');

                Volt::route('/posts', 'admin.posts.index')->name('posts.index');
                Volt::route('/posts/create', 'admin.posts.create')->name('posts.create');
                Volt::route('/posts/{post}/edit', 'admin.posts.edit')->name('posts.edit');

                Volt::route('/structure', 'admin.structure.index')->name('structure.index');
                Volt::route('/media', 'admin.media.index')->name('media.index');
                Volt::route('/settings', 'admin.settings.index')->name('settings.index');

                Route::prefix('needs')->name('needs.')->group(function () {
                    Volt::route('/', 'admin.needs.index')->name('index');
                });

                // User Management (accessible by tenant admins, scoped by branch_id)
                Route::prefix('users')->name('users.')->group(function () {
                    Volt::route('/', 'admin.users.index')->name('index');
                    Volt::route('/create', 'admin.users.create')->name('create');
                    Volt::route('/{user}/edit', 'admin.users.edit')->name('edit');
                });

                // Campaign Categories
                Route::prefix('campaign-categories')->name('campaign-categories.')->group(function () {
                    Volt::route('/', 'admin.campaign-categories.index')->name('index');
                    Volt::route('/create', 'admin.campaign-categories.create')->name('create');
                    Volt::route('/{category}/edit', 'admin.campaign-categories.edit')->name('edit');
                });

                // Banner Management
                Route::prefix('banners')->name('banners.')->group(function () {
                    Volt::route('/', 'admin.banners.index')->name('index');
                    Volt::route('/create', 'admin.banners.create')->name('create');
                    Volt::route('/{banner}/edit', 'admin.banners.edit')->name('edit');
                });

                // Super Admin Management
                Route::middleware('role:super_admin')->group(function () {
                    Volt::route('/branches', 'admin.branches.index')->name('branches.index');
                    Volt::route('/branches/create', 'admin.branches.create')->name('branches.create');
                    Volt::route('/branches/{branch}/edit', 'admin.branches.edit')->name('branches.edit');

                    // Impersonation Start
                    Route::get('/impersonate/{user}', [ImpersonationController::class, 'impersonate'])->name('impersonate');
                });
            });

            // Monitoring & Logs
            Volt::route('/monitoring', 'admin.monitoring.index')->name('admin.monitoring.index');
            Volt::route('/monitoring/{branch}', 'admin.monitoring.show')->name('admin.monitoring.show');
            Volt::route('/activities', 'admin.activities.index')->name('admin.activities.index');

            // Reporting
            Volt::route('/reports', 'admin.reports.index')->name('admin.reports.index');
        });
});

// Guest / Public Routes
Route::domain(env('APP_DOMAIN', 'lazismu.test'))->name('guest.')->group(function () {
    Route::get('/', [GuestController::class, 'home'])->name('home');

    Route::prefix('program')->name('campaigns.')->group(function () {
        Route::get('/', [GuestController::class, 'campaignsIndex'])->name('index');
        Route::get('/{slug}', [GuestController::class, 'campaignShow'])->name('show');
    });

    Route::prefix('berita')->name('posts.')->group(function () {
        Route::get('/', [GuestController::class, 'postsIndex'])->name('index');
        Route::get('/{slug}', [GuestController::class, 'postShow'])->name('show');
    });

    Route::get('/tentang', [GuestController::class, 'about'])->name('about');
    Route::get('/struktur', [GuestController::class, 'structure'])->name('structure');
    Route::get('/kontak', [GuestController::class, 'contact'])->name('contact');
    Route::get('/laporan', [GuestController::class, 'reports'])->name('reports');
    Route::get('/zakat-kalkulator', [GuestController::class, 'calculator'])->name('calculator');

    Route::prefix('cabang/{branch_slug}')->name('branches.')->group(function () {
        Route::get('/', [GuestController::class, 'branchShow'])->name('show');
        Route::get('/program', [GuestController::class, 'branchCampaigns'])->name('campaigns');
    });

    Route::get('/pengajuan', [NeedController::class, 'create'])->name('needs.create');
    Route::post('/pengajuan', [NeedController::class, 'store'])->name('needs.store');
    Route::get('/cek-status', [NeedController::class, 'checkStatusForm'])->name('needs.status');
    Route::post('/cek-status', [NeedController::class, 'checkStatus'])->name('needs.status.check');

    Route::get('/halaman/{slug}', function () {
        return view('guest.pages.show');
    })->name('pages.show');

    // Donation Flow
    Route::prefix('donasi')->name('donate.')->group(function () {
        Route::post('/submit', [DonateController::class, 'submit'])->name('submit');
        Route::post('/pilih-pembayaran/{donation_id}', [DonateController::class, 'selectPayment'])->name('selectPayment');

        Route::get('/pembayaran/{donation_id}', [DonateController::class, 'payment'])->name('payment');
        Route::get('/konfirmasi/{donation_id}', [DonateController::class, 'confirm'])->name('confirm');
        Route::get('/status/{donation_id}', [DonateController::class, 'status'])->name('status');
        Route::post('/upload-proof/{donation_id}', [DonateController::class, 'uploadProof'])->name('uploadProof');

        Route::get('/berhasil/{donation}', [DonateController::class, 'success'])->name('success');

        // Put this last to avoid catching other routes
        Route::get('/{campaign_slug?}', [DonateController::class, 'form'])->name('form');
    });

    // Handle Fortify default redirect
    Route::get('/dashboard', function () {
        return redirect()->route('dashboard');
    })->middleware(['auth']);
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

// 3. Fallback Page Routes (Must be at the very bottom)
Route::get('/{slug}', function (string $slug) {
    $page = Page::where('slug', '=', $slug, true)->firstOrFail();

    return view('guest.pages.show', compact('page'));
})->name('guest.page');

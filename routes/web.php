<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

// Domain configuration
$appDomain = env('APP_DOMAIN', 'lazismu.test');
$adminDomain = env('ADMIN_DOMAIN', 'admin.lazismu.test');
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
            
            // RBAC Management
            Route::middleware('role:super_admin')->group(function () {
                Volt::route('/roles', 'admin.roles.index')->name('roles.index');
                Volt::route('/roles/create', 'admin.roles.create')->name('roles.create');
                Volt::route('/roles/{role}/edit', 'admin.roles.edit')->name('roles.edit');

                Volt::route('/permissions', 'admin.permissions.index')->name('permissions.index');
                Volt::route('/permissions/create', 'admin.permissions.create')->name('permissions.create');
                Volt::route('/permissions/{permission}/edit', 'admin.permissions.edit')->name('permissions.edit');

                Volt::route('/users', 'admin.users.index')->name('users.index');
                Volt::route('/users/create', 'admin.users.create')->name('users.create');
                Volt::route('/users/{user}/edit', 'admin.users.edit')->name('users.edit');
            });
        });

        // Reporting
        Volt::route('/reports', 'admin.reports.index')->name('admin.reports.index');
    });
});

// Guest / Public Routes
Route::domain(env('APP_DOMAIN', 'lazismu.test'))->name('guest.')->group(function () {
    Route::get('/', function () {
        return view('guest.home');
    })->name('home');

    Route::prefix('program')->name('campaigns.')->group(function () {
        Route::get('/', function () { return view('guest.campaigns.index'); })->name('index');
        Route::get('/{slug}', function ($slug) { 
            $campaign = \App\Models\Campaign::active()->where('slug', $slug)->firstOrFail();
            return view('guest.campaigns.show', compact('campaign')); 
        })->name('show');
    });

    Route::prefix('berita')->name('posts.')->group(function () {
        Route::get('/', function () { return view('guest.posts.index'); })->name('index');
        Route::get('/{slug}', function ($slug) { 
            $post = \App\Models\BlogPost::published()->where('slug', $slug)->firstOrFail();
            return view('guest.posts.show', compact('post')); 
        })->name('show');
    });

    Route::get('/tentang', function () { return view('guest.about'); })->name('about');
    Route::get('/struktur', function () { return view('guest.structure'); })->name('structure');
    Route::get('/kontak', function () { return view('guest.contact'); })->name('contact');
    Route::get('/laporan', function () { return view('guest.reports'); })->name('reports');
    Route::get('/zakat-kalkulator', function () { return view('guest.calculator'); })->name('calculator');
    
    Route::get('/halaman/{slug}', function () { return view('guest.pages.show'); })->name('pages.show');

    // Donation Flow
    Route::prefix('donasi')->name('donate.')->group(function () {
        Route::get('/{campaign_slug?}', function () { return view('guest.donate.form'); })->name('form');
        Route::get('/pembayaran', function () { return view('guest.donate.payment'); })->name('payment');
        Route::get('/konfirmasi', function () { return view('guest.donate.confirm'); })->name('confirm');
        Route::get('/berhasil', function () { return view('guest.donate.success'); })->name('success');
        Route::get('/status/{id}', function () { return view('guest.donate.status'); })->name('status');
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

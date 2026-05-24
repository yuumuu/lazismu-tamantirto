<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use App\Traits\BelongsToMasjid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use BelongsToMasjid, HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'masjid_id',
        'name',
        'email',
        'password',
        'photo',
        'last_login_at',
        'last_login_ip',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'created_by');
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'author_id');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'created_by');
    }

    public function verifiedDonations(): HasMany
    {
        return $this->hasMany(Donation::class, 'verified_by');
    }

    public function uploadedMedia(): HasMany
    {
        return $this->hasMany(MediaLibrary::class, 'uploaded_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // ==================== ACCESSORS ====================

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        return asset('storage/'.$this->photo);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->photo) {
            return $this->photo_url;
        }

        // Generate Gravatar URL
        $hash = md5(strtolower(trim($this->email)));

        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=200";
    }

    // ==================== RBAC HELPER METHODS ====================

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(UserRole::SuperAdmin->value);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin->value);
    }

    public function isEditor(): bool
    {
        return $this->hasRole(UserRole::Editor->value);
    }

    public function isViewer(): bool
    {
        return $this->hasRole(UserRole::Viewer->value);
    }

    public function canVerifyDonation(): bool
    {
        return $this->hasPermissionTo('donation.verify');
    }

    public function canPublishContent(): bool
    {
        return $this->hasAnyPermission([
            'campaign.publish',
            'blog.publish',
            'page.publish',
        ]);
    }

    public function canManageUsers(): bool
    {
        return $this->hasPermissionTo('system.manage_users');
    }

    public function canManageSettings(): bool
    {
        return $this->hasPermissionTo('system.manage_settings');
    }

    // ==================== PUBLIC METHODS ====================

    public function recordLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    public function muzakki(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Muzakki::class);
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}

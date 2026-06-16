<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'logo',
        'address',
        'phone',
        'email',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::created(function (Branch $branch) {
            User::create([
                'name' => 'Admin '.$branch->name,
                'email' => 'admin@'.($branch->slug === 'pusat' ? 'lazismu.org' : $branch->slug.'.org'),
                'password' => Hash::make('Lazismu123!'),
                'email_verified_at' => now(),
                'is_active' => true,
                'branch_id' => $branch->id,
                'role' => 'admin',
            ]);
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}

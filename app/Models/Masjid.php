<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Masjid extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'address',
        'phone',
        'email',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::created(function (Masjid $masjid) {
            $user = \App\Models\User::create([
                'name' => 'Admin '.$masjid->name,
                'email' => 'admin@'.($masjid->slug === 'pusat' ? 'lazismu.org' : $masjid->slug.'.org'),
                'password' => \Illuminate\Support\Facades\Hash::make('Lazismu123!'),
                'email_verified_at' => now(),
                'is_active' => true,
                'masjid_id' => $masjid->id,
            ]);

            $user->assignRole('admin');
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function campaigns(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function donations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function withdrawals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }
}

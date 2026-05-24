<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    use BelongsToBranch, HasFactory;

    protected $fillable = [
        'structure_id',
        'name',
        'photo',
        'email',
        'phone',
        'bio',
        'joined_date',
        'is_active',
        'sort_order',
        'social_media',
    ];

    protected function casts(): array
    {
        return [
            'joined_date' => 'date',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'social_media' => 'array',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function structure(): BelongsTo
    {
        return $this->belongsTo(OrganizationStructure::class, 'structure_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(OrganizationStructure::class, 'structure_id');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // ==================== ACCESSORS ====================

    public function getPositionAttribute(): string
    {
        return $this->structure?->name ?? 'Anggota';
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        return asset('storage/'.$this->photo);
    }

    public function getFacebookUrlAttribute(): ?string
    {
        return $this->social_media['facebook'] ?? null;
    }

    public function getInstagramUrlAttribute(): ?string
    {
        return $this->social_media['instagram'] ?? null;
    }

    public function getLinkedinUrlAttribute(): ?string
    {
        return $this->social_media['linkedin'] ?? null;
    }

    public function getYearsOfServiceAttribute(): int
    {
        if (! $this->joined_date) {
            return 0;
        }

        return (int) abs(now()->diffInYears($this->joined_date));
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'category_id',
        'created_by',
        'type',
        'title',
        'slug',
        'short_description',
        'description',
        'target_amount',
        'current_amount',
        'start_date',
        'end_date',
        'status',
        'featured_image',
        'is_featured',
        'is_urgent',
        'priority',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => CampaignType::class,
            'status' => CampaignStatus::class,
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
            'priority' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function category(): BelongsTo
    {
        return $this->belongsTo(CampaignCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(CampaignImage::class)->orderBy('sort_order');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function verifiedDonations(): HasMany
    {
        return $this->hasMany(Donation::class)->where('status', 'verified');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('status', CampaignStatus::Active);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeByType($query, CampaignType $type)
    {
        return $query->where('type', $type);
    }

    // ==================== ACCESSORS ====================

    public function getProgressPercentageAttribute(): float
    {
        return $this->calculateProgress();
    }

    public function getRemainingAmountAttribute(): float
    {
        $remaining = (float) $this->target_amount - (float) $this->current_amount;

        return max(0, $remaining);
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->end_date || $this->end_date->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($this->end_date);
    }

    public function getDonorsCountAttribute(): int
    {
        return $this->verifiedDonations()->count();
    }

    // ==================== PUBLIC METHODS ====================

    public function calculateProgress(): float
    {
        $raised = $this->totalVerifiedDonations();

        return $this->calculateProgressPercentage($raised);
    }

    public function canReceiveDonations(): bool
    {
        return $this->status->canReceiveDonations()
            && $this->end_date->isFuture();
    }

    public function publish(): void
    {
        $this->update([
            'status' => CampaignStatus::Active,
            'published_at' => now(),
        ]);
    }

    public function updateCurrentAmount(): void
    {
        $this->update([
            'current_amount' => $this->totalVerifiedDonations(),
        ]);
    }

    // ==================== PRIVATE METHODS ====================

    private function totalVerifiedDonations(): float
    {
        return (float) $this->verifiedDonations()->sum('amount');
    }

    private function calculateProgressPercentage(float $raised): float
    {
        if ($this->hasNoTarget()) {
            return 0.0;
        }

        $percentage = ($raised / (float) $this->target_amount) * 100;

        return $this->capProgressAt100Percent($percentage);
    }

    private function hasNoTarget(): bool
    {
        return (float) $this->target_amount === 0.0;
    }

    private function capProgressAt100Percent(float $percentage): float
    {
        return round(min($percentage, 100), 2);
    }
}

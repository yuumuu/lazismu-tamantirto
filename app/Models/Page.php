<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PageStatus;
use App\Traits\BelongsToMasjid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use BelongsToMasjid, HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'status',
        'template',
        'meta_title',
        'meta_description',
        'is_homepage',
        'sort_order',
        'created_by',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PageStatus::class,
            'is_homepage' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==================== SCOPES ====================

    public function scopePublished($query)
    {
        return $query->where('status', PageStatus::Published);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // ==================== ACCESSORS ====================

    public function getMetaTitleDisplayAttribute(): string
    {
        return $this->meta_title ?? $this->title;
    }

    public function getMetaDescriptionDisplayAttribute(): string
    {
        return $this->meta_description ?? $this->excerpt ?? '';
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (! $this->featured_image) {
            return null;
        }

        return asset('storage/'.$this->featured_image);
    }

    // ==================== PUBLIC METHODS ====================

    public function isPublished(): bool
    {
        return $this->status === PageStatus::Published;
    }

    public function publish(): void
    {
        $this->update([
            'status' => PageStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function unpublish(): void
    {
        $this->update([
            'status' => PageStatus::Draft,
            'published_at' => null,
        ]);
    }
}

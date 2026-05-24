<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PostStatus;
use App\Traits\BelongsToMasjid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{
    use BelongsToMasjid, HasFactory;

    protected $fillable = [
        'masjid_id',
        'category_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'status',
        'is_featured',
        'view_count',
        'meta_title',
        'meta_description',
        'author_id',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'is_featured' => 'boolean',
            'view_count' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // ==================== SCOPES ====================

    public function scopePublished($query)
    {
        return $query->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('view_count');
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('published_at');
    }

    // ==================== ACCESSORS ====================

    public function getMetaTitleDisplayAttribute(): string
    {
        return $this->meta_title ?? $this->title;
    }

    public function getMetaDescriptionDisplayAttribute(): string
    {
        return $this->meta_description ?? $this->excerpt;
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (! $this->featured_image) {
            return null;
        }

        return asset('storage/'.$this->featured_image);
    }

    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));

        return max(1, (int) ceil($wordCount / 200)); // 200 words per minute
    }

    // ==================== PUBLIC METHODS ====================

    public function isPublished(): bool
    {
        return $this->status === PostStatus::Published;
    }

    public function publish(): void
    {
        $this->update([
            'status' => PostStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}

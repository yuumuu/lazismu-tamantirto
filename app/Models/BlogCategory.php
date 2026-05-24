<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    use BelongsToBranch, HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'slug',
        'description',
        'color',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'category_id');
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

    public function getPostsCountAttribute(): int
    {
        return $this->posts()->count();
    }

    public function getPublishedPostsCountAttribute(): int
    {
        return $this->posts()->where('status', 'published')->count();
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MenuLocation;
use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Menu extends Model
{
    use BelongsToBranch, HasFactory;

    protected $fillable = [
        'name',
        'location',
        'parent_id',
        'label',
        'url',
        'page_id',
        'target',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'location' => MenuLocation::class,
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByLocation($query, MenuLocation $location)
    {
        return $query->where('location', $location);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeHeader($query)
    {
        return $query->where('location', MenuLocation::Header);
    }

    public function scopeFooter($query)
    {
        return $query->where('location', MenuLocation::Footer);
    }

    public function scopeMobile($query)
    {
        return $query->where('location', MenuLocation::Mobile);
    }

    // ==================== ACCESSORS ====================

    public function getHrefAttribute(): string
    {
        if ($this->url) {
            return $this->url;
        }

        if ($this->page) {
            return route('pages.show', $this->page->slug);
        }

        return '#';
    }

    public function getTargetAttributeAttribute(): string
    {
        return $this->target ?? '_self';
    }

    // ==================== PUBLIC METHODS ====================

    public function isExternal(): bool
    {
        return ! empty($this->url);
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Get menu tree for a location.
     */
    public static function getTree(MenuLocation $location): Collection
    {
        return static::query()
            ->where('location', $location)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children', 'page'])
            ->orderBy('sort_order')
            ->get();
    }
}

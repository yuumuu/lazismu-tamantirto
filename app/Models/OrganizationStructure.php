<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationStructure extends Model
{
    use BelongsToBranch, HasFactory;

    protected $fillable = [
        'name',
        'level',
        'parent_id',
        'description',
        'responsibilities',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function parent(): BelongsTo
    {
        return $this->belongsTo(OrganizationStructure::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(OrganizationStructure::class, 'parent_id')
            ->orderBy('sort_order');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class, 'structure_id')
            ->orderBy('sort_order');
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class, 'structure_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
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

    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('level')->orderBy('sort_order');
    }

    // ==================== ACCESSORS ====================

    public function getLevelNameAttribute(): string
    {
        return match ($this->level) {
            1 => 'Pimpinan',
            2 => 'Board',
            3 => 'Divisi',
            4 => 'Staff',
            default => 'Lainnya',
        };
    }

    public function getMembersCountAttribute(): int
    {
        return $this->members()->count();
    }

    public function getActiveMembersCountAttribute(): int
    {
        return $this->activeMembers()->count();
    }

    // ==================== PUBLIC METHODS ====================

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function hasMembers(): bool
    {
        return $this->members()->exists();
    }

    /**
     * Get all ancestors (parent, grandparent, etc.)
     */
    public function ancestors(): array
    {
        $ancestors = [];
        $current = $this->parent;

        while ($current) {
            $ancestors[] = $current;
            $current = $current->parent;
        }

        return array_reverse($ancestors);
    }

    /**
     * Get all descendants (children, grandchildren, etc.)
     */
    public function descendants(): \Illuminate\Support\Collection
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants());
        }

        return $descendants;
    }
}

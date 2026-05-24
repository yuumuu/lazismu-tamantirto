<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AsnafType;
use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mustahik extends Model
{
    use BelongsToBranch, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'asnaf_type',
        'nik',
        'family_card_number',
        'occupation',
        'income_range',
        'resides_at',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'asnaf_type' => AsnafType::class,
            'is_active' => 'boolean',
        ];
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

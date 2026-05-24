<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignImage extends Model
{
    use BelongsToBranch, HasFactory;

    protected $fillable = [
        'campaign_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    // ==================== ACCESSORS ====================

    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->image_path);
    }
}

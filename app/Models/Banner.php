<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToMasjid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use BelongsToMasjid, HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'button_text',
        'button_link',
        'image_path',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }
}

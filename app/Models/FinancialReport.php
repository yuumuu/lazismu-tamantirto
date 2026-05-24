<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialReport extends Model
{
    use BelongsToBranch, HasFactory;

    protected $fillable = [
        'title',
        'year',
        'type',
        'file_path',
        'cover_image',
        'description',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/'.$this->file_path);
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/'.$this->cover_image) : null;
    }
}

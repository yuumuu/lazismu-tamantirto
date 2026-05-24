<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaLibrary extends Model
{
    use BelongsToBranch, HasFactory;

    protected $table = 'media_library';

    protected $fillable = [
        'filename',
        'original_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'width',
        'height',
        'alt_text',
        'caption',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ==================== SCOPES ====================

    public function scopeImages($query)
    {
        return $query->where('file_type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('file_type', 'video');
    }

    public function scopeDocuments($query)
    {
        return $query->where('file_type', 'document');
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    // ==================== ACCESSORS ====================

    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' bytes';
    }

    public function getDimensionsAttribute(): ?string
    {
        if (! $this->width || ! $this->height) {
            return null;
        }

        return $this->width.'x'.$this->height;
    }

    // ==================== PUBLIC METHODS ====================

    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->file_type === 'video';
    }

    public function isDocument(): bool
    {
        return $this->file_type === 'document';
    }
}

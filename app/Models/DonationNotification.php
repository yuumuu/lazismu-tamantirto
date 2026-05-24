<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationNotification extends Model
{
    use BelongsToBranch, HasFactory;

    protected $fillable = [
        'branch_id',
        'donation_id',
        'type',
        'recipient',
        'status',
        'content',
        'retry_count',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'retry_count' => 'integer',
            'sent_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    // ==================== SCOPES ====================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ==================== PUBLIC METHODS ====================

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    public function canRetry(): bool
    {
        return $this->retry_count < 3 && $this->status === 'failed';
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLog extends Model
{
    use BelongsToBranch, HasFactory;

    protected $fillable = [
        'branch_id',
        'donation_id',
        'payment_method',
        'payment_channel',
        'amount',
        'status',
        'metadata',
        'ip_address',
        'user_agent',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'metadata' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    // ==================== ACCESSORS ====================

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp '.number_format((float) $this->amount, 0, ',', '.');
    }
}

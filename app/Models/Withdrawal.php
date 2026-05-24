<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WithdrawalStatus;
use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Withdrawal extends Model
{
    use BelongsToBranch, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'mustahik_id',
        'distributor_id',
        'amount',
        'date',
        'description',
        'status',
        'proof_image',
        'created_by',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
            'status' => WithdrawalStatus::class,
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function mustahik(): BelongsTo
    {
        return $this->belongsTo(Mustahik::class);
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getFormattedAmountAttribute(): string
    {
        return format_rupiah($this->amount);
    }

    public function getProofImageUrlAttribute(): ?string
    {
        return $this->proof_image ? Storage::disk('public')->url($this->proof_image) : null;
    }
}

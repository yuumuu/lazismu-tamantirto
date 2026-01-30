<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CampaignType;
use App\Enums\DonationStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'muzakki_id',
        'transaction_id',
        'donor_name',
        'donor_email',
        'donor_phone',
        'amount',
        'donation_type',
        'payment_method',
        'bank_name',
        'account_number',
        'status',
        'proof_image',
        'donor_message',
        'is_anonymous',
        'admin_fee',
        'is_suspicious',
        'suspicious_reason',
        'verified_at',
        'verified_by',
        'verification_notes',
    ];

    protected function casts(): array
    {
        return [
            'donation_type' => CampaignType::class,
            'payment_method' => PaymentMethod::class,
            'status' => DonationStatus::class,
            'amount' => 'decimal:2',
            'admin_fee' => 'decimal:2',
            'is_anonymous' => 'boolean',
            'is_suspicious' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Donation $donation) {
            if (empty($donation->transaction_id)) {
                $donation->transaction_id = self::generateTransactionId();
            }
        });
    }

    // ==================== RELATIONSHIPS ====================

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function muzakki(): BelongsTo
    {
        return $this->belongsTo(Muzakki::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function paymentLog(): HasOne
    {
        return $this->hasOne(PaymentLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(DonationNotification::class);
    }

    // ==================== SCOPES ====================

    public function scopePending($query)
    {
        return $query->whereIn('status', [
            DonationStatus::Pending,
            DonationStatus::PendingManual,
        ]);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', DonationStatus::Verified);
    }

    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }

    public function scopeByStatus($query, DonationStatus $status)
    {
        return $query->where('status', $status);
    }

    // ==================== ACCESSORS ====================

    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Hamba Allah';
        }

        return $this->donor_name;
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp '.number_format((float) $this->amount, 0, ',', '.');
    }

    public function getProofImageUrlAttribute(): string
    {
        return asset('storage/'.$this->proof_image);
    }

    // ==================== PUBLIC METHODS ====================

    public function isVerified(): bool
    {
        return $this->status === DonationStatus::Verified;
    }

    public function canBeVerified(): bool
    {
        return $this->status->canBeVerified();
    }

    public function canBeRejected(): bool
    {
        return $this->status->canBeRejected();
    }

    public function verify(User $verifier, ?string $notes = null): void
    {
        $this->update([
            'status' => DonationStatus::Verified,
            'verified_at' => now(),
            'verified_by' => $verifier->id,
            'verification_notes' => $notes,
        ]);

        if ($this->campaign) {
            $this->campaign->updateCurrentAmount();
        }
    }

    public function reject(User $verifier, string $reason): void
    {
        $this->update([
            'status' => DonationStatus::Rejected,
            'verified_at' => now(),
            'verified_by' => $verifier->id,
            'verification_notes' => $reason,
        ]);
    }

    public function markAsSuspicious(string $reason): void
    {
        $this->update([
            'is_suspicious' => true,
            'suspicious_reason' => $reason,
            'status' => DonationStatus::PendingManual,
        ]);
    }

    // ==================== STATIC METHODS ====================

    public static function generateTransactionId(): string
    {
        $prefix = 'LZM';
        $date = now()->format('ymd');
        $random = strtoupper(Str::random(6));

        return $prefix.$date.$random;
    }
}

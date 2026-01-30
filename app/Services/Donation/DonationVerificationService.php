<?php

declare(strict_types=1);

namespace App\Services\Donation;

use App\Enums\DonationStatus;
use App\Models\AuditLog;
use App\Models\Donation;
use App\Models\User;

class DonationVerificationService
{
    /**
     * Verify a donation.
     */
    public function verify(Donation $donation, User $verifier, ?string $notes = null): VerificationResult
    {
        if (! $this->canVerify($donation)) {
            return VerificationResult::cannotVerify('Donasi tidak dapat diverifikasi.');
        }

        $donation->verify($verifier, $notes);

        $this->logVerification($donation, $verifier, 'verify');

        return VerificationResult::success('Donasi berhasil diverifikasi.');
    }

    /**
     * Reject a donation.
     */
    public function reject(Donation $donation, User $verifier, string $reason): VerificationResult
    {
        if (! $this->canReject($donation)) {
            return VerificationResult::cannotVerify('Donasi tidak dapat ditolak.');
        }

        $donation->reject($verifier, $reason);

        $this->logVerification($donation, $verifier, 'reject');

        return VerificationResult::success('Donasi telah ditolak.');
    }

    /**
     * Check if donation can be verified.
     */
    public function canVerify(Donation $donation): bool
    {
        return $donation->canBeVerified();
    }

    /**
     * Check if donation can be rejected.
     */
    public function canReject(Donation $donation): bool
    {
        return $donation->canBeRejected();
    }

    /**
     * Get pending donations for verification.
     */
    public function getPendingDonations(int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return Donation::query()
            ->pending()
            ->with(['campaign', 'campaign.category'])
            ->orderBy('is_suspicious', 'desc')
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get suspicious donations requiring manual review.
     */
    public function getSuspiciousDonations(): \Illuminate\Database\Eloquent\Collection
    {
        return Donation::query()
            ->where('status', DonationStatus::PendingManual)
            ->where('is_suspicious', true)
            ->with(['campaign', 'campaign.category'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Log verification action.
     */
    private function logVerification(Donation $donation, User $verifier, string $action): void
    {
        AuditLog::log(
            $verifier->id,
            $action,
            Donation::class,
            (string) $donation->id,
            [
                'donation_id' => $donation->id,
                'transaction_id' => $donation->transaction_id,
                'amount' => $donation->amount,
                'status' => $donation->status->value,
            ]
        );
    }
}

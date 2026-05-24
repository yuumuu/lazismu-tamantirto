<?php

declare(strict_types=1);

namespace App\Services\Donation;

use App\Enums\DonationStatus;
use App\Enums\PaymentMethod;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\PaymentLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class DonationService
{
    private const MINIMUM_DONATION = 10_000;

    private const AUTO_VERIFY_THRESHOLD = 1_000_000;

    private const MAX_DONATIONS_PER_HOUR = 3;

    /**
     * Create a new donation from validated data.
     */
    public function create(array $data, UploadedFile $proofImage): Donation
    {
        return DB::transaction(function () use ($data, $proofImage) {
            $campaign = $this->findCampaignOrFail($data['campaign_id']);

            $this->validateCampaignCanReceiveDonations($campaign);

            $proofPath = $this->storeProofImage($proofImage);

            $donation = $this->buildDonation($data, $campaign, $proofPath);

            $this->checkAndMarkSuspiciousIfNeeded($donation);

            $donation->save();

            $this->createPaymentLog($donation, $data);

            return $donation;
        });
    }

    /**
     * Find a campaign or throw an exception.
     */
    private function findCampaignOrFail(string $campaignId): Campaign
    {
        return Campaign::findOrFail($campaignId);
    }

    /**
     * Validate that the campaign can receive donations.
     */
    private function validateCampaignCanReceiveDonations(Campaign $campaign): void
    {
        if (! $campaign->canReceiveDonations()) {
            throw new \RuntimeException('Campaign tidak dapat menerima donasi saat ini.');
        }
    }

    /**
     * Store proof image and return the path.
     */
    private function storeProofImage(UploadedFile $file): string
    {
        $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

        return $file->storeAs('donations/proofs', $filename, 'public');
    }

    /**
     * Build the donation model.
     */
    private function buildDonation(array $data, Campaign $campaign, string $proofPath): Donation
    {
        return new Donation([
            'campaign_id' => $campaign->id,
            'transaction_id' => Donation::generateTransactionId(),
            'donor_name' => $data['donor_name'],
            'donor_email' => $data['donor_email'],
            'donor_phone' => $this->normalizePhoneNumber($data['donor_phone']),
            'amount' => $data['amount'],
            'donation_type' => $data['donation_type'] ?? $campaign->type,
            'payment_method' => PaymentMethod::from($data['payment_method']),
            'bank_name' => $data['bank_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'proof_image' => $proofPath,
            'donor_message' => $data['donor_message'] ?? null,
            'is_anonymous' => $data['is_anonymous'] ?? false,
            'status' => DonationStatus::Pending,
        ]);
    }

    /**
     * Normalize phone number to +62 format.
     */
    private function normalizePhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[\s\-]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '+62'.substr($phone, 1);
        }

        if (str_starts_with($phone, '62')) {
            return '+'.$phone;
        }

        return $phone;
    }

    /**
     * Check if donation is suspicious and mark accordingly.
     */
    private function checkAndMarkSuspiciousIfNeeded(Donation $donation): void
    {
        $result = $this->checkSuspicious($donation);

        if ($result['is_suspicious']) {
            $donation->is_suspicious = true;
            $donation->suspicious_reason = implode('; ', $result['reasons']);
            $donation->status = DonationStatus::PendingManual;
        }
    }

    /**
     * Check if donation is suspicious.
     */
    public function checkSuspicious(Donation $donation): array
    {
        $reasons = [];

        if ($this->hasInvalidAmount($donation)) {
            $reasons[] = $this->getAmountSuspicionReason($donation);
        }

        if ($this->hasInvalidPhoneFormat($donation)) {
            $reasons[] = 'Format telepon tidak valid';
        }

        if ($this->hasMultipleDonationsInShortPeriod($donation)) {
            $reasons[] = 'Terlalu banyak donasi dalam waktu singkat';
        }

        if ($this->exceedsAutoVerifyThreshold($donation)) {
            $reasons[] = 'Jumlah melebihi batas auto verifikasi';
        }

        return [
            'is_suspicious' => count($reasons) > 0,
            'reasons' => $reasons,
        ];
    }

    /**
     * Check if amount is invalid.
     */
    private function hasInvalidAmount(Donation $donation): bool
    {
        return (float) $donation->amount < self::MINIMUM_DONATION;
    }

    /**
     * Get amount suspicion reason.
     */
    private function getAmountSuspicionReason(Donation $donation): string
    {
        if ((float) $donation->amount < self::MINIMUM_DONATION) {
            return 'Jumlah kurang dari minimum ('.format_rupiah(self::MINIMUM_DONATION).')';
        }

        return '';
    }

    /**
     * Check if phone format is invalid.
     */
    private function hasInvalidPhoneFormat(Donation $donation): bool
    {
        $phone = $donation->donor_phone;

        return ! preg_match('/^(\+62|62|0)[0-9]{9,12}$/', $phone);
    }

    /**
     * Check if there are multiple donations in a short period.
     */
    private function hasMultipleDonationsInShortPeriod(Donation $donation): bool
    {
        $recentCount = Donation::query()
            ->where('donor_email', $donation->donor_email)
            ->where('created_at', '>', now()->subHour())
            ->count();

        return $recentCount >= self::MAX_DONATIONS_PER_HOUR;
    }

    /**
     * Check if donation exceeds auto verify threshold.
     */
    private function exceedsAutoVerifyThreshold(Donation $donation): bool
    {
        $threshold = (int) setting('auto_verify_threshold', self::AUTO_VERIFY_THRESHOLD);

        return (float) $donation->amount > $threshold;
    }

    /**
     * Create payment log for the donation.
     */
    private function createPaymentLog(Donation $donation, array $data): void
    {
        PaymentLog::create([
            'donation_id' => $donation->id,
            'payment_method' => $donation->payment_method->value,
            'payment_channel' => $data['payment_channel'] ?? $donation->payment_method->value,
            'amount' => $donation->amount,
            'status' => 'pending',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get donation statistics.
     */
    public function getStatistics(): array
    {
        $donations = Donation::query();

        return [
            'total_verified' => (clone $donations)->verified()->sum('amount'),
            'total_pending' => (clone $donations)->pending()->count(),
            'total_donors' => (clone $donations)->verified()->distinct('donor_email')->count(),
            'today_donations' => (clone $donations)->verified()->whereDate('verified_at', today())->sum('amount'),
            'this_month' => (clone $donations)->verified()->whereMonth('verified_at', now()->month)->sum('amount'),
        ];
    }
}

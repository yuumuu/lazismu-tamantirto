<?php

declare(strict_types=1);

namespace App\Services\Donation;

use App\Enums\WithdrawalStatus;
use App\Models\Withdrawal;
use App\Models\User;
use App\Services\Media\MediaService;
use App\Services\ServiceResult;
use Illuminate\Support\Facades\DB;
use Exception;

class WithdrawalVerificationService
{
    public function __construct(protected MediaService $mediaService) {}

    /**
     * Verify a fund withdrawal request.
     */
    public function verify(Withdrawal $withdrawal, User $verifier): ServiceResult
    {
        if ($withdrawal->status !== WithdrawalStatus::Draft) {
            return ServiceResult::error('Penarikan dana ini sudah diverifikasi sebelumnya.');
        }

        try {
            DB::transaction(function () use ($withdrawal, $verifier) {
                $withdrawal->update([
                    'status' => WithdrawalStatus::Verified,
                    'verified_by' => $verifier->id,
                ]);

                // Optional: Audit log entry
            });

            return ServiceResult::success('Penarikan dana telah diverifikasi.');
        } catch (Exception $e) {
            return ServiceResult::error('Gagal memverifikasi penarikan: ' . $e->getMessage());
        }
    }

    /**
     * Mark a withdrawal as completed/sent.
     */
    public function complete(Withdrawal $withdrawal, ?string $proofImage = null): ServiceResult
    {
        if ($withdrawal->status !== WithdrawalStatus::Verified) {
            return ServiceResult::error('Hanya penarikan yang sudah diverifikasi yang dapat ditandai selesai.');
        }

        try {
            $withdrawal->update([
                'status' => WithdrawalStatus::Sent,
                'proof_image' => $proofImage ?? $withdrawal->proof_image,
            ]);

            return ServiceResult::success('Dana telah berhasil tersalurkan.');
        } catch (Exception $e) {
            return ServiceResult::error('Gagal memperbarui status penyaluran: ' . $e->getMessage());
        }
    }
}

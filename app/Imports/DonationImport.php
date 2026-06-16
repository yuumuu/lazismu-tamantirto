<?php

declare(strict_types=1);

namespace App\Imports;

use App\Enums\CampaignType;
use App\Enums\DonationStatus;
use App\Enums\PaymentMethod;
use App\Models\Donation;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DonationImport implements SkipsOnFailure, ToModel, WithChunkReading, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public int $processedRows = 0;

    public function getProcessedRows(): int
    {
        return $this->processedRows;
    }

    public function model(array $row): Donation
    {
        $donorName = $row['nama_donatur'] ?? $row['donor_name'] ?? null;

        if ($donorName === null) {
            return new Donation;
        }

        $donationType = $this->resolveDonationType($row['tipe_donasi'] ?? $row['donation_type'] ?? 'infaq');
        $paymentMethod = $this->resolvePaymentMethod($row['metode'] ?? $row['payment_method'] ?? 'manual');
        $status = $this->resolveDonationStatus($row['status'] ?? 'pending');

        $this->processedRows++;

        return new Donation([
            'donor_name' => $donorName,
            'donor_email' => $row['email'] ?? $row['donor_email'] ?? null,
            'donor_phone' => $row['telepon'] ?? $row['donor_phone'] ?? null,
            'amount' => $row['jumlah'] ?? $row['amount'] ?? null,
            'donation_type' => $donationType,
            'payment_method' => $paymentMethod,
            'status' => $status,
            'bank_name' => $row['bank'] ?? $row['bank_name'] ?? null,
            'account_number' => $row['no_rekening'] ?? $row['account_number'] ?? null,
            'donor_message' => $row['pesan'] ?? $row['donor_message'] ?? null,
            'is_anonymous' => filter_var($row['anonim'] ?? $row['is_anonymous'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_donatur' => ['required_without:donor_name', 'string', 'max:255'],
            'donor_name' => ['required_without:nama_donatur', 'string', 'max:255'],
            'jumlah' => ['required_without:amount', 'numeric', 'min:0'],
            'amount' => ['required_without:jumlah', 'numeric', 'min:0'],
            'email' => ['sometimes', 'email', 'max:255'],
            'donor_email' => ['sometimes', 'email', 'max:255'],
            'telepon' => ['sometimes', 'string', 'max:20'],
            'donor_phone' => ['sometimes', 'string', 'max:20'],
            'tipe_donasi' => ['sometimes', 'string', Rule::in(CampaignType::values())],
            'donation_type' => ['sometimes', 'string', Rule::in(CampaignType::values())],
            'metode' => ['sometimes', 'string', Rule::in(PaymentMethod::values())],
            'payment_method' => ['sometimes', 'string', Rule::in(PaymentMethod::values())],
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function headingRow(): int
    {
        return 1;
    }

    private function resolveDonationType(string $value): CampaignType
    {
        $normalized = Str::lower(trim($value));

        return CampaignType::tryFrom($normalized) ?? CampaignType::Infaq;
    }

    private function resolvePaymentMethod(string $value): PaymentMethod
    {
        $normalized = Str::lower(trim($value));

        return PaymentMethod::tryFrom($normalized) ?? PaymentMethod::Manual;
    }

    private function resolveDonationStatus(string $value): DonationStatus
    {
        $normalized = Str::lower(trim($value));

        return DonationStatus::tryFrom($normalized) ?? DonationStatus::Pending;
    }
}

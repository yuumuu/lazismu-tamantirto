<?php

declare(strict_types=1);

namespace App\Imports;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Models\Campaign;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CampaignImport implements SkipsOnFailure, ToModel, WithChunkReading, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public int $processedRows = 0;

    public function getProcessedRows(): int
    {
        return $this->processedRows;
    }

    public function model(array $row): Campaign
    {
        $title = $row['judul'] ?? $row['title'] ?? null;

        if ($title === null) {
            return new Campaign;
        }

        $type = $this->resolveCampaignType($row['tipe'] ?? $row['type'] ?? 'infaq');
        $status = $this->resolveCampaignStatus($row['status'] ?? 'draft');

        $this->processedRows++;

        return new Campaign([
            'id' => (string) Str::uuid(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(4)),
            'description' => $row['deskripsi'] ?? $row['description'] ?? null,
            'short_description' => $row['deskripsi_singkat'] ?? $row['short_description'] ?? null,
            'target_amount' => $row['target'],
            'current_amount' => $row['terkumpul'] ?? $row['current_amount'] ?? 0,
            'type' => $type,
            'status' => $status,
            'start_date' => $row['tanggal_mulai'] ?? $row['start_date'] ?? now(),
            'end_date' => $row['tanggal_selesai'] ?? $row['end_date'] ?? null,
            'is_featured' => filter_var($row['unggulan'] ?? $row['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_urgent' => filter_var($row['mendesak'] ?? $row['is_urgent'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'priority' => (int) ($row['prioritas'] ?? $row['priority'] ?? 0),
            'featured_image' => $row['link_gambar'] ?? $row['featured_image'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'judul' => ['required_without:title', 'string', 'max:255'],
            'title' => ['required_without:judul', 'string', 'max:255'],
            'deskripsi' => ['required_without:description', 'string'],
            'description' => ['required_without:deskripsi', 'string'],
            'target' => ['required', 'numeric', 'min:0'],
            'tipe' => ['sometimes', 'string', Rule::in(CampaignType::values())],
            'type' => ['sometimes', 'string', Rule::in(CampaignType::values())],
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

    private function resolveCampaignType(string $value): CampaignType
    {
        $normalized = Str::lower(trim($value));

        return CampaignType::tryFrom($normalized) ?? CampaignType::Infaq;
    }

    private function resolveCampaignStatus(string $value): CampaignStatus
    {
        $normalized = Str::lower(trim($value));

        return CampaignStatus::tryFrom($normalized) ?? CampaignStatus::Draft;
    }
}

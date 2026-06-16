<?php

declare(strict_types=1);

namespace App\Imports;

use App\Enums\AsnafType;
use App\Models\Mustahik;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MustahikImport implements SkipsOnFailure, ToModel, WithChunkReading, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public int $processedRows = 0;

    public function getProcessedRows(): int
    {
        return $this->processedRows;
    }

    public function model(array $row)
    {
        $asnafMap = [
            'fakir' => AsnafType::Fakir,
            'miskin' => AsnafType::Miskin,
            'amil' => AsnafType::Amil,
            'muallaf' => AsnafType::Mualaf,
            'riqab' => AsnafType::Riqab,
            'gharim' => AsnafType::Gharim,
            'fisabilillah' => AsnafType::Fisabilillah,
            'ibnu sabil' => AsnafType::IbnuSabil,
        ];

        $asnafInput = strtolower(trim($row['asnaf'] ?? 'fakir'));
        $asnafType = $asnafMap[$asnafInput] ?? AsnafType::Fakir;

        $this->processedRows++;

        return new Mustahik([
            'id' => (string) Str::uuid(),
            'name' => $row['nama'],
            'address' => $row['alamat'] ?? null,
            'phone' => $row['telepon'] ?? $row['no_hp'] ?? $row['phone'] ?? null,
            'asnaf_type' => $asnafType,
            'nik' => $row['nik'] ?? null,
            'family_card_number' => $row['no_kk'] ?? $row['family_card_number'] ?? null,
            'occupation' => $row['pekerjaan'] ?? $row['occupation'] ?? null,
            'income_range' => $row['penghasilan'] ?? $row['income_range'] ?? null,
            'resides_at' => $row['domisili'] ?? $row['resides_at'] ?? null,
            'notes' => $row['catatan'] ?? $row['notes'] ?? null,
            'is_active' => filter_var($row['aktif'] ?? $row['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20',
            'telepon' => 'nullable|string|max:20',
            'no_hp' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
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
}

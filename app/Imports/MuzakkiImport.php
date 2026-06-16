<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Muzakki;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MuzakkiImport implements SkipsOnFailure, ToModel, WithChunkReading, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public int $processedRows = 0;

    public function getProcessedRows(): int
    {
        return $this->processedRows;
    }

    public function model(array $row)
    {
        $this->processedRows++;

        return new Muzakki([
            'id' => (string) Str::uuid(),
            'name' => $row['nama'],
            'email' => $row['email'] ?? null,
            'phone' => $row['telepon'] ?? $row['no_hp'] ?? $row['phone'],
            'address' => $row['alamat'] ?? null,
            'nik' => $row['nik'] ?? null,
            'npwp' => $row['npwp'] ?? null,
            'type' => $row['tipe'] ?? $row['type'] ?? 'perorangan',
            'is_active' => filter_var($row['aktif'] ?? $row['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'no_hp' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'nik' => 'nullable|string|max:20',
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

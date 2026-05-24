<?php

use App\Models\Muzakki;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $file;
    public $importCount = 0;
    public $errorCount = 0;
    public $errors = [];

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:csv,txt|max:5120', // Max 5MB
        ]);

        $path = $this->file->getRealPath();
        $file = fopen($path, 'r');

        $header = fgetcsv($file); // Assume first row is header
        // Header expected: name, email, phone, address

        $imported = 0;
        $errors = 0;
        $errorList = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($file)) !== false) {
                if (count($row) < 3) continue; // Skip malformed rows

                $name = $row[0] ?? '';
                $email = $row[1] ?? null;
                $phone = $row[2] ?? null;
                $address = $row[3] ?? null;

                if (empty($name) || empty($phone)) {
                    $errors++;
                    $errorList[] = "Baris $imported: Nama atau Nomor HP kosong.";
                    continue;
                }

                Muzakki::updateOrCreate(
                    [
                        'phone' => $phone,
                        'branch_id' => session('active_branch_id', 1),
                    ],
                    [
                        'name' => $name,
                        'email' => $email,
                        'address' => $address,
                        'is_active' => true,
                    ]
                );

                $imported++;
            }
            DB::commit();

            $this->importCount = $imported;
            $this->errorCount = $errors;
            $this->errors = $errorList;

            session()->flash('success', "Berhasil mengimpor $imported Muzakki.");
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', "Gagal mengimpor file: " . $e->getMessage());
        }

        fclose($file);
    }
}
?>

<x-layouts.admin>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Import Muzakki (CSV)</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('admin.muzakkis.index') }}" class="btn bg-white border-slate-200 hover:border-slate-300 text-slate-500 hover:text-slate-600">
                    Kembali
                </a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-sm border border-slate-200 p-6">
            <form wire:submit="import">
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1">Pilih File CSV <span class="text-rose-500">*</span></label>
                    <input type="file" wire:model="file" class="form-input w-full" accept=".csv" required />
                    @error('file') <div class="text-xs text-rose-500 mt-1">{{ $message }}</div> @enderror
                    <div class="text-xs text-slate-500 mt-2">Format CSV yang diharapkan: Nama, Email, Nomor HP, Alamat (Baris pertama dianggap sebagai Header).</div>
                </div>

                <div>
                    <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="import">Upload & Import</span>
                        <span wire:loading wire:target="import">Mengimpor...</span>
                    </button>
                </div>
            </form>

            @if(session()->has('success'))
                <div class="mt-4 p-4 bg-emerald-50 text-emerald-600 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if(session()->has('error'))
                <div class="mt-4 p-4 bg-rose-50 text-rose-600 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            @if(count($errors) > 0)
                <div class="mt-4 p-4 bg-amber-50 text-amber-600 rounded-md">
                    <h4 class="font-bold mb-2">Terdapat {{ $errorCount }} error:</h4>
                    <ul class="list-disc pl-5">
                        @foreach($errors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>

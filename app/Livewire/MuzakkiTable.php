<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Muzakki;
use Illuminate\Database\Eloquent\Builder;

class MuzakkiTable extends DataTableComponent
{
    protected $model = Muzakki::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setThAttributes(function(Column $column) {
                return ['class' => 'font-mono text-[10px] uppercase tracking-widest text-zinc-400'];
            })
            ->setTableAttributes(['class' => 'min-w-full divide-y divide-zinc-100 dark:divide-zinc-800'])
            ->setSearchBlur();
    }

    public function columns(): array
    {
        return [
            Column::make("Nama", "name")
                ->sortable()
                ->searchable()
                ->format(fn($value, $row) => view('components.admin.table.entity-cell', ['name' => $value, 'meta' => 'NIK: ' . ($row->nik ?? '-')])),
            
            Column::make("Kontak", "email")
                ->sortable()
                ->searchable()
                ->format(fn($value, $row) => view('components.admin.table.contact-cell', ['email' => $value, 'phone' => $row->phone])),

            Column::make("Tipe", "type")
                ->sortable()
                ->format(fn($value) => view('components.admin.table.badge-cell', ['label' => ucfirst($value)])),

            Column::make("Status", "is_active")
                ->sortable()
                ->format(fn($value, $row) => view('components.admin.table.toggle-cell', ['active' => $value, 'id' => $row->id, 'action' => 'toggleStatus'])),

            Column::make("Aksi", "id")
                ->format(fn($value, $row) => view('components.admin.table.actions-cell', [
                    'edit' => route('admin.muzakkis.edit', $row),
                    'delete' => $value,
                    'confirm' => 'Hapus data muzakki ini?'
                ])),
        ];
    }

    public function toggleStatus(string $id): void
    {
        $muzakki = Muzakki::findOrFail($id);
        $muzakki->update(['is_active' => !$muzakki->is_active]);
        $this->dispatch('notify', message: 'Status Muzakki diperbarui.', type: 'success');
    }

    public function delete(string $id): void
    {
        Muzakki::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Data Muzakki berhasil dihapus.', type: 'success');
    }
}

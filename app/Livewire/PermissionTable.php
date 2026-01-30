<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Spatie\Permission\Models\Permission;

class PermissionTable extends DataTableComponent
{
    protected $model = Permission::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setThAttributes(fn(Column $column) => ['class' => 'font-mono text-[10px] uppercase tracking-widest text-zinc-400'])
            ->setTableAttributes(['class' => 'min-w-full divide-y divide-zinc-100 dark:divide-zinc-800'])
            ->setSearchBlur();
    }

    public function columns(): array
    {
        return [
            Column::make("Nama Izin", "name")
                ->sortable()
                ->searchable()
                ->format(fn($value) => '<span class="text-xs font-mono font-bold text-zinc-700 bg-zinc-100 px-2 py-1 rounded">' . $value . '</span>')
                ->html(),

            Column::make("Guard", "guard_name")
                ->sortable()
                ->format(fn($value) => '<span class="text-[10px] uppercase text-zinc-400 font-mono">' . $value . '</span>')
                ->html(),

            Column::make("Aksi", "id")
                ->format(fn($value, $row) => view('components.admin.table.actions-cell', [
                    'edit' => route('admin.permissions.edit', $row),
                    'delete' => $value,
                    'confirm' => 'Hapus izin ini?'
                ])),
        ];
    }

    public function delete(string $id): void
    {
        Permission::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Izin berhasil dihapus.', type: 'success');
    }
}

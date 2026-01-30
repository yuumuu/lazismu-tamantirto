<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Spatie\Permission\Models\Role;

class RoleTable extends DataTableComponent
{
    protected $model = Role::class;

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
            Column::make("Nama Role", "name")
                ->sortable()
                ->searchable()
                ->format(fn($value) => view('components.admin.table.entity-cell', ['name' => strtoupper($value), 'meta' => 'GUARD: web'])),

            Column::make("Izin Aktif", "id")
                ->format(fn($value, $row) => '<span class="text-xs font-mono text-zinc-500">' . $row->permissions_count . ' Permission</span>')
                ->html(),

            Column::make("Aksi", "id")
                ->format(fn($value, $row) => view('components.admin.table.actions-cell', [
                    'edit' => route('admin.roles.edit', $row),
                    'delete' => $value,
                    'confirm' => 'Hapus role ini?'
                ])),
        ];
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return Role::query()->withCount('permissions');
    }

    public function delete(string $id): void
    {
        Role::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Role berhasil dihapus.', type: 'success');
    }
}

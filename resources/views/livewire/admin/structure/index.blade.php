<?php

use App\Models\OrganizationStructure;
use App\Models\TeamMember;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $activeTab = 'structure';
    public $editingStructureId = null;
    public $editingMemberId = null;

    // Structure Form
    public $s_name, $s_level = 1, $s_parent_id, $s_sort_order = 0;
    // Member Form
    public $m_name, $m_structure_id, $m_email, $m_bio, $m_phone, $m_sort_order = 0;
    public $m_photo_upload;

    public function selectTab($tab): void
    {
        $this->activeTab = $tab;
    }

    public function editStructure($id = null): void
    {
        $this->editingStructureId = $id;
        if ($id) {
            $s = OrganizationStructure::findOrFail($id);
            $this->s_name = $s->name;
            $this->s_level = $s->level;
            $this->s_parent_id = $s->parent_id;
            $this->s_sort_order = $s->sort_order;
        } else {
            $this->reset(['s_name', 's_level', 's_parent_id', 's_sort_order']);
            $this->s_level = 1;
        }
        $this->modal('structure-modal')->show();
    }

    public function saveStructure(): void
    {
        $validated = $this->validate([
            's_name' => 'required|string|max:255',
            's_level' => 'required|integer',
            's_parent_id' => 'nullable|exists:organization_structures,id',
            's_sort_order' => 'required|integer',
        ]);

        OrganizationStructure::updateOrCreate(
            ['id' => $this->editingStructureId],
            [
                'name' => $this->s_name,
                'level' => $this->s_level,
                'parent_id' => $this->s_parent_id,
                'sort_order' => $this->s_sort_order,
            ]
        );

        $this->modal('structure-modal')->close();
        $this->dispatch('notify', message: 'Struktur berhasil disimpan.', type: 'success');
    }

    public function editMember($id = null): void
    {
        $this->editingMemberId = $id;
        if ($id) {
            $m = TeamMember::findOrFail($id);
            $this->m_name = $m->name;
            $this->m_structure_id = $m->structure_id;
            $this->m_email = $m->email;
            $this->m_phone = $m->phone;
            $this->m_bio = $m->bio;
            $this->m_sort_order = $m->sort_order;
        } else {
            $this->reset(['m_name', 'm_structure_id', 'm_email', 'm_phone', 'm_bio', 'm_sort_order', 'm_photo_upload']);
        }
        $this->modal('member-modal')->show();
    }

    public function saveMember(): void
    {
        $validated = $this->validate([
            'm_name' => 'required|string|max:255',
            'm_structure_id' => 'required|exists:organization_structures,id',
            'm_email' => 'nullable|email|max:255',
            'm_phone' => 'nullable|string|max:20',
            'm_bio' => 'nullable|string',
            'm_sort_order' => 'required|integer',
            'm_photo_upload' => 'nullable|image|max:1024',
        ]);

        $data = [
            'name' => $this->m_name,
            'structure_id' => $this->m_structure_id,
            'email' => $this->m_email,
            'phone' => $this->m_phone,
            'bio' => $this->m_bio,
            'sort_order' => $this->m_sort_order,
        ];

        if ($this->m_photo_upload) {
            $data['photo'] = $this->m_photo_upload->store('team', 'public');
        }

        TeamMember::updateOrCreate(
            ['id' => $this->editingMemberId],
            $data
        );

        $this->modal('member-modal')->close();
        $this->dispatch('notify', message: 'Anggota tim berhasil disimpan.', type: 'success');
    }

    public function deleteStructure($id): void
    {
        OrganizationStructure::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Posisi dihapus.', type: 'success');
    }

    public function deleteMember($id): void
    {
        TeamMember::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Anggota tim dihapus.', type: 'success');
    }

    public function with(): array
    {
        return [
            'structures' => OrganizationStructure::query()->with('parent')->ordered()->get(),
            'members' => TeamMember::query()->with('structure')->ordered()->get(),
            'levels' => [
                1 => 'Pimpinan',
                2 => 'Board / Pengurus',
                3 => 'Divisi',
                4 => 'Staff',
            ],
        ];
    }
} ?>

<div>
    <x-admin.page-header 
        title="Struktur Organisasi & Tim" 
        description="Kelola hierarki organisasi dan profil anggota tim LazisMU." 
    />

    <div class="p-3 md:p-6 lg:p-10 space-y-8">

    <!-- Tabs -->
    <div class="flex gap-4 border-b border-zinc-200 dark:border-zinc-800">
        <button wire:click="selectTab('structure')" class="px-4 py-2 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'structure' ? 'border-primary text-primary' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
            {{ __('Hierarki Struktur') }}
        </button>
        <button wire:click="selectTab('members')" class="px-4 py-2 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'members' ? 'border-primary text-primary' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
            {{ __('Anggota Tim') }}
        </button>
    </div>

    @if($activeTab === 'structure')
        <div class="space-y-6">
            <div class="flex justify-end">
                <flux:button variant="primary" icon="plus" wire:click="editStructure()">{{ __('Tambah Posisi') }}</flux:button>
            </div>
            
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
                <table class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                        <tr>
                            <th class="px-6 py-3">{{ __('Nama Posisi') }}</th>
                            <th class="px-6 py-3">{{ __('Level') }}</th>
                            <th class="px-6 py-3">{{ __('Atasan (Parent)') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                        @forelse($structures as $s)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-zinc-900 dark:text-white uppercase tracking-tight">{{ $s->name }}</td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" color="zinc" inset="top bottom">{{ $s->level_name }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-zinc-500">{{ $s->parent?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button variant="ghost" size="xs" icon="pencil-square" wire:click="editStructure('{{ $s->id }}')" />
                                    <flux:button variant="ghost" size="xs" icon="trash" wire:click="deleteStructure('{{ $s->id }}')" wire:confirm="Hapus posisi ini?" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" />
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-zinc-500 italic">{{ __('Belum ada data struktur.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="space-y-6">
            <div class="flex justify-end">
                <flux:button variant="primary" icon="plus" wire:click="editMember()">{{ __('Tambah Anggota') }}</flux:button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($members as $m)
                    <div class="premium-card p-6 flex gap-4 group">
                        <div class="size-20 rounded-2xl overflow-hidden bg-zinc-100 shrink-0 border border-zinc-200 dark:border-white/5">
                            <img src="{{ $m->photo_url ?? '/images/placeholder-user.jpg' }}" class="size-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-black text-zinc-900 dark:text-white truncate uppercase tracking-tight">{{ $m->name }}</h4>
                            <p class="text-xs text-primary font-bold uppercase">{{ $m->structure->name }}</p>
                            <div class="mt-2 space-y-1">
                                <p class="text-[10px] text-zinc-500 flex items-center gap-1.5"><flux:icon name="envelope" class="size-3" /> {{ $m->email ?: '-' }}</p>
                                <p class="text-[10px] text-zinc-500 flex items-center gap-1.5"><flux:icon name="phone" class="size-3" /> {{ $m->phone ?: '-' }}</p>
                            </div>
                            
                            <div class="flex gap-2 mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                <flux:button variant="ghost" size="xs" icon="pencil-square" wire:click="editMember('{{ $m->id }}')" />
                                <flux:button variant="ghost" size="xs" icon="trash" wire:click="deleteMember('{{ $m->id }}')" wire:confirm="Hapus anggota ini?" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20" />
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-3xl">
                        <p class="text-zinc-500 font-medium italic">Belum ada data anggota tim.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- Modals -->
    <flux:modal name="structure-modal" class="md:w-[500px]">
        <form wire:submit="saveStructure" class="space-y-6">
            <div>
                <flux:heading >{{ $editingStructureId ? 'Edit Posisi' : 'Tambah Posisi' }}</flux:heading>
                <flux:subheading>Atur tingkatan dan hierarki organisasi.</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:input label="Nama Posisi" wire:model="s_name" placeholder="E.g. Ketua Umum, Sekretaris, dll" />
                
                <flux:select label="Level" wire:model="s_level">
                    @foreach($levels as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </flux:select>

                <flux:select label="Parent (Atasan Langsung)" wire:model="s_parent_id" placeholder="Pilih atasan jika ada">
                    <option value="">None</option>
                    @foreach($structures as $s)
                        @if($s->id != $editingStructureId)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endif
                    @endforeach
                </flux:select>

                <flux:input label="Urutan" type="number" wire:model="s_sort_order" />
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Simpan Posisi</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="member-modal" class="md:w-[600px]">
        <form wire:submit="saveMember" class="space-y-6">
            <div>
                <flux:heading >{{ $editingMemberId ? 'Edit Anggota' : 'Tambah Anggota' }}</flux:heading>
                <flux:subheading>Lengkapi profil dan jabatan anggota tim.</flux:subheading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <flux:input label="Nama Lengkap" wire:model="m_name" />
                </div>
                
                <flux:select label="Jabatan/Struktur" wire:model="m_structure_id">
                    <option value="">Pilih Jabatan</option>
                    @foreach($structures as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </flux:select>

                <flux:input label="Email" wire:model="m_email" type="email" />
                <flux:input label="No. WhatsApp" wire:model="m_phone" />
                <flux:input label="Urutan" type="number" wire:model="m_sort_order" />
                
                <div class="md:col-span-2">
                    <flux:textarea label="Bio Singkat" wire:model="m_bio" rows="3" />
                </div>

                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label>Foto Profil</flux:label>
                        <div class="mt-2 flex items-center gap-4">
                            <div class="size-16 rounded-xl bg-zinc-100 overflow-hidden shrink-0">
                                @if($m_photo_upload)
                                    <img src="{{ $m_photo_upload->temporaryUrl() }}" class="size-full object-cover">
                                @else
                                    <flux:icon name="user" class="size-full p-4 text-zinc-300" />
                                @endif
                            </div>
                            <flux:input type="file" wire:model="m_photo_upload" class="flex-1" />
                        </div>
                    </flux:field>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Simpan Anggota</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

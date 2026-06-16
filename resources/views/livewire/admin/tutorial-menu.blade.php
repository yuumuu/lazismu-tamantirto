<?php

use Livewire\Volt\Component;

new class extends Component {
    public bool $showModal = false;

    public function getAvailableTutorialsProperty()
    {
        $userRole = auth()->check() ? (auth()->user()->role?->value ?? 'viewer') : 'guest';
        
        $allTutorials = [
            [
                'name' => 'dashboard-overview',
                'title' => 'Pengenalan Dashboard',
                'description' => 'Pelajari fitur-fitur utama dashboard dan statistik penting',
                'icon' => 'chart-bar',
                'roles' => ['super_admin', 'admin', 'editor', 'viewer']
            ],
            [
                'name' => 'campaign-management',
                'title' => 'Manajemen Campaign',
                'description' => 'Cara membuat, mengedit, dan mengelola campaign penggalangan dana',
                'icon' => 'megaphone',
                'roles' => ['super_admin', 'admin', 'editor']
            ],
            [
                'name' => 'donation-verification',
                'title' => 'Verifikasi Donasi',
                'description' => 'Proses verifikasi donasi yang masuk dan pengelolaan bukti transfer',
                'icon' => 'shield-check',
                'roles' => ['super_admin', 'admin']
            ],
            [
                'name' => 'user-management',
                'title' => 'Manajemen User',
                'description' => 'Kelola user, role, dan hak akses sistem',
                'icon' => 'users',
                'roles' => ['super_admin']
            ],
            [
                'name' => 'role-permission',
                'title' => 'Role & Permission',
                'description' => 'Atur role dan permission untuk kontrol akses yang granular',
                'icon' => 'key',
                'roles' => ['super_admin']
            ],
            [
                'name' => 'settings-overview',
                'title' => 'Pengaturan Sistem',
                'description' => 'Konfigurasi pengaturan aplikasi dan profil user',
                'icon' => 'cog-6-tooth',
                'roles' => ['super_admin', 'admin']
            ],
            [
                'name' => 'reports-overview',
                'title' => 'Laporan & Analitik',
                'description' => 'Memahami laporan keuangan dan data analitik sistem',
                'icon' => 'document-chart-bar',
                'roles' => ['super_admin', 'admin', 'editor']
            ]
        ];

        return collect($allTutorials)->filter(function ($tutorial) use ($userRole) {
            return in_array($userRole, $tutorial['roles']);
        })->values()->all();
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }
}; ?>

<div>
    <!-- Tutorial Button -->
    <flux:button 
        variant="ghost" 
        size="sm" 
        icon="academic-cap" 
        wire:click="openModal"
        class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100"
    >
        Tutorial
    </flux:button>

    <!-- Tutorial Modal -->
    <flux:modal name="tutorial-modal" wire:model="showModal" class="max-w-2xl">
        <div class="space-y-6">
            <div class="text-center">
                <div class="mx-auto w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center mb-4">
                    <flux:icon.academic-cap class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:heading size="lg">Tutorial Sistem</flux:heading>
                <flux:text variant="subtle">Pilih tutorial yang ingin Anda pelajari</flux:text>
            </div>

            <div class="grid gap-4 max-h-96 overflow-y-auto">
                @forelse($this->availableTutorials as $tutorial)
                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                <flux:icon :name="$tutorial['icon']" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <flux:heading size="sm">{{ $tutorial['title'] }}</flux:heading>
                                    <flux:button 
                                        size="xs" 
                                        variant="primary"
                                        x-on:click="startTutorial('{{ $tutorial['name'] }}'); $wire.closeModal()"
                                    >
                                        Mulai
                                    </flux:button>
                                </div>
                                <flux:text variant="subtle" class="text-sm mt-1">
                                    {{ $tutorial['description'] }}
                                </flux:text>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <flux:icon.exclamation-triangle class="w-12 h-12 text-zinc-400 mx-auto mb-4" />
                        <flux:text variant="subtle">Tidak ada tutorial yang tersedia untuk role Anda.</flux:text>
                    </div>
                @endforelse
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button variant="ghost" wire:click="closeModal">
                    Tutup
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

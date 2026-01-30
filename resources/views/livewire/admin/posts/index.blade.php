<?php

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Enums\PostStatus;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $category = '';
    public $status = '';
    public $totalActivitiesCount = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleFeatured(string $id): void
    {
        $post = BlogPost::findOrFail($id);
        $post->update(['is_featured' => !$post->is_featured]);
        $this->dispatch('notify', message: 'Status unggulan diperbarui.', type: 'success');
    }

    public function delete(string $id): void
    {
        BlogPost::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Artikel berhasil dihapus.', type: 'success');
    }

    public function with(): array
    {
        return [
            'posts' => BlogPost::query()
                ->with(['category', 'author'])
                ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
                ->when($this->category, fn($q) => $q->where('category_id', $this->category))
                ->when($this->status, fn($q) => $q->where('status', $this->status))
                ->latest()
                ->paginate(15),
            'categories' => BlogCategory::active()->ordered()->get(),
            'statuses' => PostStatus::cases(),
            'recentActivities' => $this->getRecentActivities(),
        ];
    }

    private function getRecentActivities(): \Illuminate\Support\Collection
    {
        // Get recent donations (all statuses)
        $donations = \App\Models\Donation::query()
            ->with(['campaign', 'verifier'])
            ->latest()
            ->limit(20) // Get more to check if we need "View All" button
            ->get()
            ->map(function ($donation) {
                return [
                    'type' => 'donation',
                    'id' => $donation->id,
                    'title' => 'Donasi dari ' . ($donation->is_anonymous ? 'Hamba Allah' : $donation->donor_name),
                    'description' => 'Untuk ' . ($donation->campaign?->title ?? 'Dana Umum'),
                    'amount' => $donation->formatted_amount,
                    'status' => $donation->status,
                    'is_verified' => $donation->status->value === 'verified',
                    'is_suspicious' => $donation->is_suspicious,
                    'created_at' => $donation->created_at,
                    'verifier' => $donation->verifier?->name,
                    'verification_notes' => $donation->verification_notes,
                ];
            });

        // Get recent audit logs for verification activities
        $auditLogs = \App\Models\AuditLog::query()
            ->with('user')
            ->whereIn('action', ['verify', 'reject'])
            ->where('model', \App\Models\Donation::class)
            ->latest()
            ->limit(20) // Get more to check if we need "View All" button
            ->get()
            ->map(function ($log) {
                $changes = json_decode($log->changes, true);
                return [
                    'type' => 'verification',
                    'id' => $log->id,
                    'title' => ucfirst($log->action) . ' donasi',
                    'description' => 'ID Transaksi: ' . ($changes['transaction_id'] ?? 'N/A'),
                    'amount' => isset($changes['amount']) ? 'Rp ' . number_format($changes['amount'], 0, ',', '.') : null,
                    'status' => $log->action === 'verify' ? 'verified' : 'rejected',
                    'is_verified' => $log->action === 'verify',
                    'is_suspicious' => false,
                    'created_at' => $log->created_at,
                    'verifier' => $log->user?->name,
                    'verification_notes' => null,
                ];
            });

        // Combine and sort by created_at, then take only 15 for display
        $allActivities = $donations->concat($auditLogs)
            ->sortByDesc('created_at');

        // Store total count for "View All" button logic
        $this->totalActivitiesCount = $allActivities->count();

        return $allActivities->take(15);
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Berita & Artikel') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg leading-relaxed border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Kelola berita, artikel edukasi, dan inspirasi donasi.') }}
            </p>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('admin.posts.create')" wire:navigate>
            {{ __('Tulis Artikel') }}
        </flux:button>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="w-full md:w-1/3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari judul artikel..." icon="magnifying-glass" />
        </div>
        <div class="w-full md:w-1/4">
            <flux:select wire:model.live="category" placeholder="Semua Kategori">
                <option value="">{{ __('Semua Kategori') }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-full md:w-1/4">
            <flux:select wire:model.live="status" placeholder="Semua Status">
                <option value="">{{ __('Semua Status') }}</option>
                @foreach($statuses as $s)
                    <option value="{{ $s->value }}">{{ $s->label() }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <!-- Table -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
        <table class="w-full text-sm text-left">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-6 py-3">{{ __('Artikel') }}</th>
                    <th class="px-6 py-3">{{ __('Kategori') }}</th>
                    <th class="px-6 py-3">{{ __('Views') }}</th>
                    <th class="px-6 py-3">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse($posts as $post)
                <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <img src="{{ $post->featured_image_url ?? '/images/placeholder-post.jpg' }}" class="size-12 rounded-lg object-cover bg-zinc-100">
                            <div class="flex flex-col">
                                <span class="font-medium text-zinc-900 dark:text-white line-clamp-1">{{ $post->title }}</span>
                                <span class="text-[10px] text-zinc-500">{{ $post->author?->name ?? 'Admin' }} • {{ $post->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <flux:badge size="sm" variant="outline">{{ $post->category?->name ?? 'Uncategorized' }}</flux:badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1 text-zinc-500">
                            <flux:icon name="eye" class="size-4" />
                            <span>{{ $post->view_count }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <flux:badge :color="$post->status->color()" size="sm" inset="top">
                                {{ $post->status->label() }}
                            </flux:badge>
                            @if($post->is_featured)
                                <flux:tooltip content="Muncul di Hero/Featured">
                                    <flux:icon name="star" variant="solid" class="size-4 text-amber-500" />
                                </flux:tooltip>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <flux:button variant="ghost" size="sm" icon="pencil-square" :href="route('admin.posts.edit', $post)" wire:navigate />
                            <flux:button variant="ghost" size="sm" icon="trash" wire:click="delete('{{ $post->id }}')" wire:confirm="Hapus artikel ini?" class="text-red-500" />
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-zinc-500">{{ __('Belum ada artikel.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-6 border-t border-zinc-100 dark:border-zinc-800">
            {{ $posts->links() }}
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
        <div class="bg-zinc-50 dark:bg-zinc-900/50 px-6 py-4 border-b border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center gap-2">
                <flux:icon name="clock" class="size-5 text-zinc-500" />
                <h2 class="text-lg font-bold text-zinc-900 dark:text-white">Aktivitas Terbaru</h2>
                <flux:badge size="sm" variant="outline" class="ml-auto">{{ $recentActivities->count() }} aktivitas</flux:badge>
            </div>
            <p class="text-sm text-zinc-500 mt-1">Semua aktivitas donasi dan verifikasi terbaru</p>
        </div>

        <div class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
            @forelse($recentActivities as $activity)
                <div class="px-6 py-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                    <div class="flex items-start gap-4">
                        <!-- Activity Icon -->
                        <div class="flex-shrink-0 mt-1">
                            @if($activity['type'] === 'donation')
                                @if($activity['is_verified'])
                                    <div class="size-8 rounded-full bg-lime-100 dark:bg-lime-900/30 flex items-center justify-center">
                                        <flux:icon name="check-circle" class="size-4 text-lime-600" />
                                    </div>
                                @elseif($activity['is_suspicious'])
                                    <div class="size-8 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                        <flux:icon name="exclamation-triangle" class="size-4 text-orange-600" />
                                    </div>
                                @else
                                    <div class="size-8 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                        <flux:icon name="clock" class="size-4 text-amber-600" />
                                    </div>
                                @endif
                            @else
                                @if($activity['is_verified'])
                                    <div class="size-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                        <flux:icon name="shield-check" class="size-4 text-blue-600" />
                                    </div>
                                @else
                                    <div class="size-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                        <flux:icon name="x-circle" class="size-4 text-red-600" />
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Activity Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-sm font-medium text-zinc-900 dark:text-white">
                                            {{ $activity['title'] }}
                                        </h3>
                                        
                                        <!-- Status Badge -->
                                        @if($activity['type'] === 'donation')
                                            @if($activity['is_verified'])
                                                <flux:badge color="lime" size="xs">Terverifikasi</flux:badge>
                                            @elseif($activity['is_suspicious'])
                                                <flux:badge color="orange" size="xs">Mencurigakan</flux:badge>
                                            @else
                                                <flux:badge color="amber" size="xs">Menunggu</flux:badge>
                                            @endif
                                        @else
                                            @if($activity['is_verified'])
                                                <flux:badge color="blue" size="xs">Diverifikasi</flux:badge>
                                            @else
                                                <flux:badge color="red" size="xs">Ditolak</flux:badge>
                                            @endif
                                        @endif
                                    </div>

                                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">
                                        {{ $activity['description'] }}
                                    </p>

                                    @if($activity['amount'])
                                        <p class="text-sm font-mono font-bold text-primary">
                                            {{ $activity['amount'] }}
                                        </p>
                                    @endif

                                    @if($activity['verifier'])
                                        <p class="text-xs text-zinc-500 mt-1">
                                            <flux:icon name="user" class="size-3 inline" />
                                            Oleh: {{ $activity['verifier'] }}
                                        </p>
                                    @endif

                                    @if($activity['verification_notes'])
                                        <p class="text-xs text-zinc-500 mt-1 italic">
                                            "{{ $activity['verification_notes'] }}"
                                        </p>
                                    @endif
                                </div>

                                <!-- Timestamp -->
                                <div class="flex-shrink-0 text-right">
                                    <p class="text-xs text-zinc-500">
                                        {{ $activity['created_at']->diffForHumans() }}
                                    </p>
                                    <p class="text-xs text-zinc-400">
                                        {{ $activity['created_at']->format('d M, H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center">
                    <flux:icon name="clock" class="size-12 text-zinc-300 mx-auto mb-3" />
                    <p class="text-zinc-500 text-sm">Belum ada aktivitas terbaru</p>
                </div>
            @endforelse
        </div>

        @if($totalActivitiesCount > 15)
            <div class="bg-zinc-50 dark:bg-zinc-900/50 px-6 py-3 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button variant="ghost" size="sm" class="w-full" :href="route('admin.donations.index')" wire:navigate>
                    <flux:icon name="arrow-right" class="size-4" />
                    Lihat Semua Aktivitas
                </flux:button>
            </div>
        @endif
    </div>
</div>

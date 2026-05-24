<?php

use App\Models\Donation;
use App\Models\AuditLog;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with(): array
    {
        return [
            'recentActivities' => $this->getRecentActivities(),
        ];
    }

    private function getRecentActivities(): \Illuminate\Pagination\LengthAwarePaginator
    {
        // Get recent donations (all statuses)
        $donations = Donation::query()
            ->with(['campaign', 'verifier'])
            ->latest()
            ->limit(100)
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
        $auditLogs = AuditLog::query()
            ->with('user')
            ->whereIn('action', ['verify', 'reject'])
            ->where('model', Donation::class)
            ->latest()
            ->limit(100)
            ->get()
            ->map(function ($log) {
                $changes = is_array($log->changes) ? $log->changes : json_decode($log->changes, true);
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

        $allActivities = $donations->concat($auditLogs)
            ->sortByDesc('created_at');

        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 15;
        $items = $allActivities->slice(($page - 1) * $perPage, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allActivities->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-6 md:space-y-8">
    <x-admin.page-header 
        title="Aktivitas Terbaru"
        description="Pantau semua aktivitas sistem terbaru, termasuk donasi dan verifikasi."
    />

    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
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

        <div class="p-6 border-t border-zinc-100 dark:border-zinc-800">
            {{ $recentActivities->links() }}
        </div>
    </div>
</div>

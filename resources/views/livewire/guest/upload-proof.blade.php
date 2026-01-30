<div class="space-y-8 animate-fade-in">
    <!-- Payment Instructions -->
    <div class="bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5 shadow-2xl overflow-hidden">
        <div class="p-8 md:p-12 space-y-10">
            <div class="text-center space-y-2">
                <h2 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Menunggu Pembayaran</h2>
                <p class="text-zinc-500 font-medium">Silakan selesaikan pembayaran sesuai instruksi di bawah.</p>
            </div>

            @if($donation->payment_method->value === 'qris')
                <div class="flex flex-col items-center gap-8">
                    <div class="p-6 bg-white rounded-3xl shadow-xl border border-zinc-100 flex flex-col items-center gap-4">
                        <img src="{{ setting('qris_image') ? asset('storage/'.setting('qris_image')) : 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=LAZISMU-TAMANTIRTO' }}" class="size-64 object-contain">
                        <span class="text-xs font-black text-zinc-400 uppercase tracking-widest">Pindai Dengan E-Wallet Apa Saja</span>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-black text-zinc-900 dark:text-white">QRIS LazisMU Tamantirto</p>
                        <p class="text-sm font-bold text-primary">ID: LZM000123456</p>
                    </div>
                </div>
            @elseif($donation->payment_method->value === 'bank_transfer')
                <div class="space-y-4">
                    @php
                        $banks = setting('bank_accounts', []);
                    @endphp
                    @foreach($banks as $bank)
                        <div class="p-6 rounded-3xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex items-center gap-4">
                                <div class="size-14 rounded-2xl bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-white/5 flex items-center justify-center font-black text-primary text-sm uppercase">
                                    {{ substr($bank['bank_name'], 0, 3) }}
                                </div>
                                <div>
                                    <h4 class="font-black text-zinc-900 dark:text-white tracking-tight">{{ $bank['bank_name'] }}</h4>
                                    <p class="text-xs text-zinc-500 font-medium">{{ $bank['account_name'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xl font-mono font-black text-zinc-900 dark:text-white">{{ $bank['account_number'] }}</span>
                                <button type="button" @click="$clipboard('{{ $bank['account_number'] }}'); $dispatch('notify', { message: 'Nomor rekening disalin!' })" class="p-2 hover:bg-zinc-200 dark:hover:bg-white/10 rounded-xl text-zinc-400 transition-colors">
                                    <flux:icon.document-duplicate class="size-5" />
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Summary -->
            <div class="p-8 rounded-[32px] bg-primary/5 dark:bg-primary/10 border border-primary/10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="text-[10px] text-zinc-400 font-black uppercase tracking-[0.2em]">Total Nominal</span>
                    <h3 class="text-4xl font-black text-primary tracking-tighter">{{ format_rupiah($donation->amount) }}</h3>
                </div>
                <flux:button variant="ghost" class="font-bold text-zinc-500 border-zinc-200 dark:border-white/10 rounded-2xl h-14">Detail Donasi</flux:button>
            </div>
        </div>
    </div>

    <!-- Proof Upload Form -->
    <div class="bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5 shadow-2xl p-8 md:p-12 space-y-8">
        <div class="text-center space-y-2">
            <h3 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Konfirmasi Pembayaran</h3>
            <p class="text-sm text-zinc-500 font-medium italic">Sudah membayar? Unggah bukti transfer Anda di sini.</p>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="space-y-4">
                @if ($proof_image)
                    <div class="relative w-full aspect-video rounded-3xl overflow-hidden border border-zinc-200 dark:border-white/5 group">
                        <img src="{{ $proof_image->temporaryUrl() }}" class="size-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button type="button" wire:click="$set('proof_image', null)" class="p-4 bg-red-500 text-white rounded-2xl hover:bg-red-600 shadow-xl transition-transform hover:scale-110">
                                <flux:icon.trash class="size-6" />
                            </button>
                        </div>
                    </div>
                @else
                    <label class="flex flex-col items-center justify-center w-full aspect-video rounded-3xl border-2 border-dashed border-zinc-200 dark:border-white/5 hover:border-primary transition-all cursor-pointer bg-zinc-50 dark:bg-zinc-950 group">
                        <div class="p-6 rounded-3xl bg-white dark:bg-zinc-900 shadow-xl mb-6 group-hover:scale-110 transition-transform">
                            <flux:icon.camera class="size-10 text-primary" />
                        </div>
                        <span class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors">{{ __('Klik untuk Unggah Bukti') }}</span>
                        <span class="text-[10px] text-zinc-400 mt-2 uppercase tracking-widest">{{ __('PNG, JPG, WEBP (Max 2MB)') }}</span>
                        <input type="file" wire:model="proof_image" class="hidden" accept="image/*">
                    </label>
                @endif
                @error('proof_image') <p class="text-xs text-red-500 font-bold mt-2 text-center">{{ $message }}</p> @enderror
            </div>

            <flux:button type="submit" variant="primary"  class="w-full h-16 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-primary/30" wire:loading.attr="disabled">
                <span wire:loading.remove>Konfirmasi Pembayaran</span>
                <span wire:loading>Memproses...</span>
            </flux:button>
        </form>
    </div>
</div>

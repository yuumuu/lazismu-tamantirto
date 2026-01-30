<?php

use App\Models\Banner;
use App\Http\Requests\StoreBannerRequest;
use App\Services\Media\MediaService;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $title = '';
    public $subtitle = '';
    public $button_text = '';
    public $button_link = '';
    public $image;
    public $order = 0;
    public $is_active = true;

    public function save(MediaService $mediaService): void
    {
        $rules = (new StoreBannerRequest())->rules();
        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'button_text' => $this->button_text,
            'button_link' => $this->button_link,
            'order' => $this->order,
            'is_active' => $this->is_active,
        ];

        if ($this->image) {
            $media = $mediaService->upload($this->image, auth()->user());
            $data['image_path'] = $media->url;
        }

        Banner::create($data);

        $this->dispatch('notify', message: 'Banner berhasil dibuat.', type: 'success');
        $this->redirect(route('admin.banners.index'), navigate: true);
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.banners.index')" wire:navigate />
        <div>
            <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Tambah Banner Baru') }}</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{{ __('Banners akan ditampilkan di hero section halaman utama.') }}</p>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <!-- Text Content -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="title" label="Judul Utama (Title)" placeholder="Contoh: Zakat Anda, Harapan Mereka" />
                    <flux:input wire:model="button_text" label="Teks Tombol (CTA)" placeholder="Contoh: Donasi Sekarang" />
                </div>
                
                <flux:textarea wire:model="subtitle" label="Subjudul / Deskripsi" placeholder="Penjelasan singkat mengenai banner ini..." rows="3" />

                <flux:input wire:model="button_link" label="Link Tombol" placeholder="Contoh: /donasi atau https://..." prefix="{{ url('/') }}" />
            </div>

            <!-- Image Upload -->
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <h3 class="font-black text-zinc-900 dark:text-white mb-6 uppercase tracking-wider text-sm">{{ __('Media Banner') }}</h3>
                
                <div class="space-y-4">
                    @if ($image)
                        <div class="relative w-full aspect-[21/9] rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 group">
                            <img src="{{ $image->temporaryUrl() }}" class="size-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button type="button" wire:click="$set('image', null)" class="p-3 bg-red-500 text-white rounded-2xl hover:bg-red-600 shadow-xl transition-transform hover:scale-110">
                                    <flux:icon.trash class="size-6" />
                                </button>
                            </div>
                        </div>
                    @else
                        <label class="flex flex-col items-center justify-center w-full aspect-[21/9] rounded-2xl border-2 border-dashed border-zinc-200 dark:border-zinc-800 hover:border-primary transition-all cursor-pointer bg-zinc-50 dark:bg-zinc-950 group">
                            <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 shadow-lg mb-4 group-hover:scale-110 transition-transform">
                                <flux:icon.cloud-arrow-up class="size-10 text-primary" />
                            </div>
                            <span class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors">{{ __('Upload Gambar Banner') }}</span>
                            <span class="text-[10px] text-zinc-400 mt-1 uppercase tracking-widest">{{ __('PNG, JPG, WEBP (Recomended: 1920x820)') }}</span>
                            <input type="file" wire:model="image" class="hidden" accept="image/*">
                        </label>
                    @endif
                    @error('image') <p class="text-xs text-red-500 font-bold mt-2">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Sidebar Options -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-8">
                <div>
                     <flux:input wire:model="order" type="number" label="Urutan Tampil" description="Angka lebih kecil tampil lebih dulu." />
                </div>

                <div class="space-y-4 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:switch wire:model="is_active" label="Aktifkan Banner" description="Banner akan langsung tampil di publik." />
                </div>

                <div class="pt-4">
                    <flux:button type="submit" variant="primary"  class="w-full font-bold shadow-lg shadow-primary/20" icon="check">
                        {{ __('Simpan Banner') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div>

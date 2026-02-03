<?php

use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Enums\CampaignType;
use App\Enums\CampaignStatus;
use App\Http\Requests\StoreCampaignRequest;
use App\Services\Media\MediaService;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public $category_id;
    public $type = '';
    public $title = '';
    public $slug = '';
    public $short_description = '';
    public $description = '';
    public $target_amount;
    public $start_date;
    public $end_date;
    public $featured_image;
    public $status = '';
    public $is_featured = false;
    public $is_urgent = false;

    public function mount(): void
    {
        $this->type = CampaignType::Infaq->value;
        $this->status = CampaignStatus::Draft->value;
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addMonth()->format('Y-m-d');
    }

    public function updatedTitle(): void
    {
        $this->slug = Str::slug($this->title);
    }

    public function save(MediaService $mediaService): void
    {
        // Manual validation using the request's rules
        $rules = (new StoreCampaignRequest())->rules();
        $this->validate($rules);

        $data = [
            'category_id' => $this->category_id,
            'type' => $this->type,
            'title' => $this->title,
            'slug' => $this->slug ?: Str::slug($this->title),
            'short_description' => $this->short_description,
            'description' => $this->description,
            'target_amount' => $this->target_amount,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => CampaignStatus::Draft->value,
            'is_featured' => $this->is_featured,
            'is_urgent' => $this->is_urgent,
            'created_by' => auth()->id(),
        ];

        if ($this->featured_image) {
            $media = $mediaService->upload($this->featured_image, auth()->user());
            $data['featured_image'] = $media->file_path;
        }

        Campaign::create($data);

        $this->dispatch('notify', message: 'Campaign berhasil dibuat.', type: 'success');
        $this->redirect(route('admin.campaigns.index'), navigate: true);
    }

    public function with(): array
    {
        return [
            'categories' => CampaignCategory::active()->ordered()->get(),
            'types' => CampaignType::cases(),
        ];
    }
} ?>

<div>
    <x-admin.page-header 
        title="Buat Campaign Baru" 
        description="Isi detail di bawah untuk membuat program donasi baru."
        backRoute="admin.campaigns.index"
    />

    <div class="p-3 md:p-6 lg:p-10 space-y-8">

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <!-- Title & Slug -->
                <div class="space-y-4">
                    <flux:input wire:model.live.debounce.500ms="title" label="Judul Campaign" placeholder="Contoh: Bantuan Beasiswa untuk Anak Yatim" />
                    <flux:input wire:model="slug" label="Slug (URL)" placeholder="judul-campaign-otomatis" prefix="{{ url('/campaigns/') }}/" />
                </div>

                <!-- Descriptions -->
                <div class="space-y-4">
                    <flux:textarea wire:model="short_description" label="Deskripsi Singkat" placeholder="Penjelasan singkat yang muncul di kartu campaign..." rows="3" />
                    
                    <div class="space-y-2">
                        <flux:label>Deskripsi Lengkap</flux:label>
                        <x-quill-editor wire:model="description" />
                        @error('description') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Content Images / Gallery -->
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">{{ __('Gambar Banner') }}</h3>
                
                <div class="space-y-4">
                    @if ($featured_image)
                        <div class="relative w-full aspect-video rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800">
                            <img src="{{ $featured_image->temporaryUrl() }}" class="size-full object-cover">
                            <button type="button" wire:click="$set('featured_image', null)" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600">
                                <flux:icon name="x-mark" class="size-4" />
                            </button>
                        </div>
                    @else
                        <label class="flex flex-col items-center justify-center w-full aspect-video rounded-2xl border-2 border-dashed border-zinc-200 dark:border-zinc-800 hover:border-amber-500 transition-colors cursor-pointer bg-zinc-50 dark:bg-zinc-950">
                            <flux:icon name="cloud-arrow-up" class="size-10 text-zinc-400 mb-2" />
                            <span class="text-sm text-zinc-500">{{ __('Klik untuk upload gambar banner') }}</span>
                            <span class="text-[10px] text-zinc-400 mt-1">PNG, JPG, WEBP (Max 2MB)</span>
                            <input type="file" wire:model="featured_image" class="hidden" accept="image/*">
                        </label>
                    @endif
                    @error('featured_image') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Sidebar Options -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <!-- Info Section -->
                <div class="space-y-4">
                    <flux:select wire:model="category_id" label="Kategori" placeholder="Pilih Kategori">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="type" label="Tipe Donasi">
                        @foreach($types as $t)
                            <option value="{{ $t->value }}">{{ $t->label() }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Logistics -->
                <div class="space-y-4">
                    <flux:input wire:model="target_amount" type="number" step="1000" label="Target Donasi (Rp)" prefix="Rp" placeholder="1000000" />
                    
                    <div class="grid grid-cols-1 gap-4">
                        <flux:input wire:model="start_date" type="date" label="Tanggal Mulai" />
                        <flux:input wire:model="end_date" type="date" label="Tanggal Berakhir" />
                    </div>
                </div>

                <!-- Flags -->
                <div class="space-y-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:switch wire:model="is_featured" label="Tampilkan di Unggulan" description="Campaign akan muncul di section utama." />
                    <flux:switch wire:model="is_urgent" label="Status Mendesak" description="Beri label mendesak/darurat." />
                </div>

                <div class="pt-4">
                    <flux:button type="submit" variant="primary" class="w-full" icon="check">
                        {{ __('Simpan Campaign') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div>

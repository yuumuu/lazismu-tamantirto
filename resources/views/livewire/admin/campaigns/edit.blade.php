<?php

use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Enums\CampaignType;
use App\Enums\CampaignStatus;
use App\Services\Media\MediaService;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public Campaign $campaign;

    public $category_id;
    public $type;
    public $title;
    public $slug;
    public $short_description;
    public $description;
    public $target_amount;
    public $start_date;
    public $end_date;
    public $status;
    public $featured_image;
    public $is_featured;
    public $is_urgent;

    public function mount(Campaign $campaign): void
    {
        $this->campaign = $campaign;

        $this->category_id = $campaign->category_id;
        $this->type = $campaign->type->value;
        $this->title = $campaign->title;
        $this->slug = $campaign->slug;
        $this->short_description = $campaign->short_description;
        $this->description = $campaign->description;
        $this->target_amount = $campaign->target_amount;
        $this->start_date = $campaign->start_date->format('Y-m-d');
        $this->end_date = $campaign->end_date->format('Y-m-d');
        $this->status = $campaign->status->value;
        $this->is_featured = $campaign->is_featured;
        $this->is_urgent = $campaign->is_urgent;
    }

    public function updatedTitle(): void
    {
        if ($this->campaign->status === CampaignStatus::Draft) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function save(MediaService $mediaService): void
    {
        $rules = [
            'category_id' => 'required|exists:campaign_categories,id',
            'title' => 'required|min:10|max:255',
            'slug' => 'required|max:255|unique:campaigns,slug,' . $this->campaign->id,
            'short_description' => 'required|min:50|max:500',
            'description' => 'required|min:100',
            'target_amount' => 'required|numeric|min:100000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string',
        ];

        $this->validate($rules);

        $data = [
            'category_id' => $this->category_id,
            'type' => $this->type,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'target_amount' => $this->target_amount,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'is_urgent' => $this->is_urgent,
        ];

        if ($this->featured_image) {
            $media = $mediaService->upload($this->featured_image, auth()->user());
            $data['featured_image'] = $media->file_path;
        }

        $this->campaign->update($data);

        $this->dispatch('notify', message: 'Campaign berhasil diperbarui.', type: 'success');
        $this->redirect(route('admin.campaigns.index'), navigate: true);
    }

    public function with(): array
    {
        return [
            'categories' => CampaignCategory::active()->ordered()->get(),
            'types' => CampaignType::cases(),
            'statuses' => CampaignStatus::cases(),
        ];
    }
}; ?>

<div>
    <x-admin.page-header 
        title="Edit Campaign" 
        description="Perbarui informasi program donasi."
        backRoute="admin.campaigns.index"
    />

    <div class="p-3 md:p-6 lg:p-10 space-y-8">

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div
                class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <!-- Title & Slug -->
                <div class="space-y-4">
                    <flux:input wire:model.live.debounce.500ms="title" label="Judul Campaign" />
                    <flux:input wire:model="slug" label="Slug (URL)" prefix="{{ url('/campaigns/') }}/" />
                </div>

                <!-- Descriptions -->
                <div class="space-y-4">
                    <flux:textarea wire:model="short_description" label="Deskripsi Singkat" rows="3" />

                    <div class="space-y-2">
                        <flux:label>Deskripsi Lengkap</flux:label>
                        <x-quill-editor wire:model="description" />
                        @error('description')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Banner Image -->
            <div
                class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">{{ __('Gambar Banner') }}</h3>

                <div class="space-y-4">
                    @if ($featured_image)
                        <div
                            class="relative w-full aspect-video rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800">
                            <img src="{{ $featured_image->temporaryUrl() }}" class="size-full object-cover">
                            <button type="button" wire:click="$set('featured_image', null)"
                                class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600">
                                <flux:icon name="x-mark" class="size-4" />
                            </button>
                        </div>
                    @elseif ($campaign->featured_image)
                        <div
                            class="relative w-full aspect-video rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800">
                            <img src="{{ $campaign->featured_image_url }}" class="size-full object-cover">
                            <label
                                class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer text-white">
                                <flux:icon name="camera" class="size-6 mr-2" />
                                <span>Ganti Gambar</span>
                                <input type="file" wire:model="featured_image" class="hidden">
                            </label>
                        </div>
                    @else
                        <label
                            class="flex flex-col items-center justify-center w-full aspect-video rounded-2xl border-2 border-dashed border-zinc-200 dark:border-zinc-800 hover:border-amber-500 transition-colors cursor-pointer">
                            <flux:icon name="cloud-arrow-up" class="size-10 text-zinc-400 mb-2" />
                            <input type="file" wire:model="featured_image" class="hidden">
                        </label>
                    @endif
                    @error('featured_image')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Sidebar Options -->
        <div class="space-y-6">
            <div
                class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <!-- Status & Category -->
                <div class="space-y-4">
                    <flux:select wire:model="status" label="Status">
                        @foreach ($statuses as $s)
                            <option value="{{ $s->value }}">{{ $s->label() }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="category_id" label="Kategori">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="type" label="Tipe Donasi">
                        @foreach ($types as $t)
                            <option value="{{ $t->value }}">{{ $t->label() }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Financials -->
                <div class="space-y-4">
                    <flux:input wire:model="target_amount" type="number" label="Target Donasi (Rp)" prefix="Rp" />

                    <div class="grid grid-cols-1 gap-4">
                        <flux:input wire:model="start_date" type="date" label="Tanggal Mulai" />
                        <flux:input wire:model="end_date" type="date" label="Tanggal Berakhir" />
                    </div>
                </div>

                <!-- Flags -->
                <div class="space-y-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:switch wire:model="is_featured" label="Unggulan" />
                    <flux:switch wire:model="is_urgent" label="Mendesak" />
                </div>

                <div class="pt-4">
                    <flux:button type="submit" variant="primary" class="w-full" icon="check">
                        {{ __('Simpan Perubahan') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php

use App\Models\Page;
use App\Enums\PageStatus;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public $title = '';
    public $slug = '';
    public $content = '';
    public $excerpt = '';
    public $featured_image;
    public $status = 'draft';
    public $template = 'default';
    public $meta_title = '';
    public $meta_description = '';
    public $is_homepage = false;
    public $sort_order = 0;

    public function updatedTitle(): void
    {
        $this->slug = Str::slug($this->title);
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|min:3|max:255',
            'slug' => 'required|unique:pages,slug',
            'content' => 'required',
            'status' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'template' => $this->template,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'is_homepage' => $this->is_homepage,
            'sort_order' => $this->sort_order,
            'created_by' => auth()->id(),
            'published_at' => $this->status === PageStatus::Published->value ? now() : null,
        ];

        if ($this->featured_image) {
            $data['featured_image'] = $this->featured_image->store('pages', 'public');
        }

        Page::create($data);

        $this->dispatch('notify', message: 'Halaman berhasil dibuat.', type: 'success');
        $this->redirect(route('admin.pages.index'), navigate: true);
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.pages.index')" wire:navigate />
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Buat Halaman') }}</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{{ __('Tulis konten untuk halaman baru.') }}</p>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <div class="space-y-4">
                    <flux:input wire:model.live.debounce.500ms="title" label="Judul Halaman" placeholder="Tentang Kami" />
                    <flux:input wire:model="slug" label="Slug (URL)" prefix="{{ url('/pages/') }}/" placeholder="tentang-kami" />
                </div>

                <div class="space-y-2">
                    <flux:label>{{ __('Konten Halaman') }}</flux:label>
                    <x-quill-editor wire:model="content" />
                    @error('content') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <flux:textarea wire:model="excerpt" label="Ringkasan Singkat (Excerpt)" placeholder="Muncul di list halaman atau meta description..." rows="3" />
            </div>

            <!-- SEO Settings -->
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <h3 class="font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    <flux:icon name="magnifying-glass" class="size-5 text-zinc-400" />
                    {{ __('Pengaturan SEO') }}
                </h3>
                
                <div class="space-y-4">
                    <flux:input wire:model="meta_title" label="Meta Title" placeholder="Judul untuk Google..." />
                    <flux:textarea wire:model="meta_description" label="Meta Description" placeholder="Deskripsi untuk pencarian..." rows="2" />
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <flux:select wire:model="status" label="Status Publikasi">
                    <option value="draft">Draft (Arsip)</option>
                    <option value="published">Published (Publikasikan)</option>
                </flux:select>

                <flux:select wire:model="template" label="Template Halaman">
                    <option value="default">Default (Standard)</option>
                    <option value="fullwidth">Full Width</option>
                    <option value="sidebar">With Sidebar</option>
                </flux:select>

                <div class="space-y-3">
                    <flux:switch wire:model="is_homepage" label="Set as Homepage" description="Gunakan halaman ini sebagai beranda utama." />
                </div>

                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <h4 class="font-bold text-sm text-zinc-900 dark:text-white mb-4">{{ __('Featured Image') }}</h4>
                    @if ($featured_image)
                        <div class="relative aspect-video rounded-xl overflow-hidden border border-zinc-200 mb-4">
                            <img src="{{ $featured_image->temporaryUrl() }}" class="size-full object-cover">
                        </div>
                    @endif
                    <input type="file" wire:model="featured_image" class="text-xs text-zinc-500">
                </div>

                <div class="pt-4">
                    <flux:button type="submit" variant="primary" class="w-full" icon="check">
                        {{ __('Simpan Halaman') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div>

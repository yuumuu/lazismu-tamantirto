<?php

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Enums\PostStatus;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public $category_id = '';
    public $title = '';
    public $slug = '';
    public $content = '';
    public $excerpt = '';
    public $featured_image;
    public $status = 'draft';
    public $is_featured = false;
    public $meta_title = '';
    public $meta_description = '';

    public function updatedTitle(): void
    {
        $this->slug = Str::slug($this->title);
    }

    public function save(): void
    {
        $this->validate([
            'category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|min:10|max:255',
            'slug' => 'required|unique:blog_posts,slug',
            'content' => 'required|min:50',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'category_id' => $this->category_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'author_id' => auth()->id(),
            'published_at' => $this->status === PostStatus::Published->value ? now() : null,
        ];

        if ($this->featured_image) {
            $data['featured_image'] = $this->featured_image->store('blog', 'public');
        }

        BlogPost::create($data);

        $this->dispatch('notify', message: 'Artikel berhasil diterbitkan.', type: 'success');
        $this->redirect(route('admin.posts.index'), navigate: true);
    }

    public function with(): array
    {
        return [
            'categories' => BlogCategory::active()->ordered()->get(),
            'statuses' => PostStatus::cases(),
        ];
    }
} ?>

<div>
    <x-admin.page-header 
        title="Tulis Artikel" 
        description="Buat berita atau konten edukatif baru."
        backRoute="admin.posts.index"
    />

    <div class="p-3 md:p-6 lg:p-10 space-y-8">

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <div class="space-y-4">
                    <flux:input wire:model.live.debounce.500ms="title" label="Judul Artikel" placeholder="Contoh: Pentingnya Berbagi di Bulan Ramadhan" />
                    <flux:input wire:model="slug" label="Slug (URL)" prefix="{{ url('/blog/') }}/" placeholder="pentingnya-berbagi-ramadhan" />
                </div>

                <div class="space-y-2">
                    <flux:label>{{ __('Isi Artikel') }}</flux:label>
                    <x-quill-editor wire:model="content" class="mt-1" />
                    @error('content') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <flux:textarea wire:model="excerpt" label="Ringkasan Singkat" placeholder="Potongan kalimat yang muncul di list berita..." rows="3" />
            </div>

            <!-- SEO Settings -->
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <h3 class="font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    <flux:icon name="magnifying-glass" class="size-5 text-zinc-400" />
                    {{ __('Optimasi Mesin Pencari (SEO)') }}
                </h3>
                
                <div class="space-y-4">
                    <flux:input wire:model="meta_title" label="Meta Title" placeholder="Judul Halaman Pencarian" />
                    <flux:textarea wire:model="meta_description" label="Meta Description" placeholder="Deskripsi untuk Google..." rows="2" />
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <flux:select wire:model="status" label="Status Artikel">
                    <option value="draft">Draft (Arsip)</option>
                    <option value="published">Published (Tayang)</option>
                    <option value="scheduled">Scheduled (Terjadwal)</option>
                </flux:select>

                <flux:select wire:model="category_id" label="Kategori">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </flux:select>

                <div class="space-y-3">
                    <flux:switch wire:model="is_featured" label="Artikel Unggulan" description="Tampilkan di slide utama atau bagian depan." />
                </div>

                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <h4 class="font-bold text-sm text-zinc-900 dark:text-white mb-4">{{ __('Gambar Utama') }}</h4>
                    @if ($featured_image)
                        <div class="relative aspect-video rounded-xl overflow-hidden border border-zinc-200 mb-4">
                            <img src="{{ $featured_image->temporaryUrl() }}" class="size-full object-cover">
                        </div>
                    @endif
                    <label
                        for="featured_image"
                        class="premium-card hover-lift border-dashed cursor-pointer flex flex-col items-center justify-center gap-3 p-6 text-center"
                    >
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-50 dark:bg-orange-950">
                            <svg
                                class="h-6 w-6 text-orange-500 dark:text-orange-400"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16"
                                />
                            </svg>
                        </div>

                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                Klik untuk upload gambar
                            </p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                JPG, PNG • Maks 2MB
                            </p>
                        </div>

                        <span class="status-pill bg-orange-50 text-orange-600 dark:bg-orange-900 dark:text-orange-300">
                            Pilih File
                        </span>

                        <input
                            id="featured_image"
                            type="file"
                            wire:model="featured_image"
                            accept="image/*"
                            class="hidden"
                        />
                    </label>
                    @error('featured_image')
                        <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                    @enderror

                    @if ($featured_image)
                        <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                            File dipilih:
                            <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                {{ $featured_image->getClientOriginalName() }}
                            </span>
                        </p>
                    @endif
                </div>

                <div class="pt-4">
                    <flux:button type="submit" variant="primary" class="w-full" icon="check">
                        {{ __('Simpan Artikel') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Enums\PostStatus;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public BlogPost $post;
    public $category_id;
    public $title;
    public $slug;
    public $content;
    public $excerpt;
    public $featured_image;
    public $status;
    public $is_featured;
    public $meta_title;
    public $meta_description;

    public function mount(BlogPost $post): void
    {
        $this->post = $post;
        $this->category_id = $post->category_id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->content = $post->content;
        $this->excerpt = $post->excerpt;
        $this->status = $post->status->value;
        $this->is_featured = $post->is_featured;
        $this->meta_title = $post->meta_title;
        $this->meta_description = $post->meta_description;
    }

    public function updatedTitle(): void
    {
        if ($this->status !== PostStatus::Published->value) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function save(): void
    {
        $this->validate([
            'category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|min:10|max:255',
            'slug' => 'required|unique:blog_posts,slug,' . $this->post->id,
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
        ];

        if ($this->status === PostStatus::Published->value && ! $this->post->published_at) {
            $data['published_at'] = now();
        }

        if ($this->featured_image) {
            $data['featured_image'] = $this->featured_image->store('blog', 'public');
        }

        $this->post->update($data);

        $this->dispatch('notify', message: 'Artikel berhasil diperbarui.', type: 'success');
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

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.posts.index')" wire:navigate />
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Edit Artikel') }}</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{{ __('Perbarui isi berita atau konten edukatif.') }}</p>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <div class="space-y-4">
                    <flux:input wire:model.live.debounce.500ms="title" label="Judul Artikel" />
                    <flux:input wire:model="slug" label="Slug (URL)" prefix="{{ url('/blog/') }}/" />
                </div>

                <div class="space-y-2">
                    <flux:label>{{ __('Isi Artikel') }}</flux:label>
                    <x-quill-editor wire:model="content" />
                    @error('content') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <flux:textarea wire:model="excerpt" label="Ringkasan Singkat" rows="3" />
            </div>

            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <h3 class="font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                    <flux:icon name="magnifying-glass" class="size-5 text-zinc-400" />
                    {{ __('Optimasi Mesin Pencari (SEO)') }}
                </h3>
                
                <div class="space-y-4">
                    <flux:input wire:model="meta_title" label="Meta Title" />
                    <flux:textarea wire:model="meta_description" label="Meta Description" rows="2" />
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

                <flux:switch wire:model="is_featured" label="Artikel Unggulan" />

                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <h4 class="font-bold text-sm text-zinc-900 dark:text-white mb-4">{{ __('Gambar Utama') }}</h4>
                    
                    @if ($featured_image)
                        <div class="relative aspect-video rounded-xl overflow-hidden border border-zinc-200 mb-4">
                            <img src="{{ $featured_image->temporaryUrl() }}" class="size-full object-cover">
                        </div>
                    @elseif ($post->featured_image)
                        <div class="relative aspect-video rounded-xl overflow-hidden border border-zinc-200 group mb-4">
                            <img src="{{ $post->featured_image_url }}" class="size-full object-cover">
                        </div>
                    @endif
                    
                    <input type="file" wire:model="featured_image" class="text-xs text-zinc-500">
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

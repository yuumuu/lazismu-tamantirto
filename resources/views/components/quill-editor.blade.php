@props(['value' => ''])

<div
    x-data="{ 
        content: @entangle($attributes->wire('model')),
        quill: null,
        initQuill() {
            if (this.quill) return;
            
            this.quill = new Quill(this.$refs.editor, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['link', 'blockquote', 'code-block', 'image'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                }
            });

            this.quill.on('text-change', () => {
                this.content = this.quill.root.innerHTML;
            });

            if (this.content) {
                this.quill.root.innerHTML = this.content;
            }

            this.$watch('content', value => {
                if (value !== this.quill.root.innerHTML) {
                    this.quill.root.innerHTML = value || '';
                }
            });
        }
    }"
    x-init="
        if (window.Quill) {
            initQuill();
        } else {
            document.addEventListener('DOMContentLoaded', () => initQuill());
        }
    "
    wire:ignore
    {{ $attributes->whereDoesntStartWith('wire:model')->class(['rounded-md border-none overflow-hidden bg-white dark:bg-zinc-900 block w-full']) }}
>
    <div x-ref="editor" style="min-height: 300px;" class="prose dark:prose-invert max-w-none"></div>
</div>


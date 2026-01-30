<div class="flex justify-end gap-2 opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity duration-300">
    <flux:button variant="ghost" size="sm" icon="pencil-square" :href="$edit" wire:navigate class="text-zinc-400 hover:text-primary transition-colors" />
    <flux:button variant="ghost" size="sm" icon="trash" wire:click="delete('{{ $delete }}')" wire:confirm="{{ $confirm }}" class="text-zinc-400 hover:text-red-500 transition-colors" />
</div>

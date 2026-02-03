<flux:radio.group 
    x-data 
    x-init="$flux.appearance = localStorage.getItem('appearance') || 'system'" 
    x-on:change="localStorage.setItem('appearance', $flux.appearance)"
    variant="segmented" 
    x-model="$flux.appearance" 
    {{ $attributes->merge(['class' => 'p-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-xl']) }}
>
    <flux:radio value="light" icon="sun" class="!p-1.5 !rounded-lg" />
    <flux:radio value="dark" icon="moon" class="!p-1.5 !rounded-lg" />
    <flux:radio value="system" icon="monitor" class="!p-1.5 !rounded-lg" />
</flux:radio.group>

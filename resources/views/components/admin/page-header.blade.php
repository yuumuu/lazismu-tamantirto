@props([
    'title',
    'description' => null,
    'backUrl' => null,
    'backRoute' => null,
    'action' => null
])

@php
    $backHref = $backUrl ?? ($backRoute ? route($backRoute) : null);
@endphp

<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 md:gap-6">
    <div class="space-y-2">
        @if($backHref)
            <div class="flex items-center gap-3 md:gap-4 mb-3 md:mb-4">
                <flux:button variant="ghost" icon="arrow-left" :href="$backHref" wire:navigate size="sm" />
                <div class="h-5 md:h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-xl md:text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $title }}</h1>
            </div>
        @else
            <div class="flex items-center gap-2">
                <div class="h-5 md:h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-xl md:text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $title }}</h1>
            </div>
        @endif
        
        @if($description)
            <p class="text-zinc-500 dark:text-zinc-400 text-xs md:text-sm max-w-lg leading-relaxed {{ $backHref ? 'pl-8 md:pl-12' : 'border-l pl-3 md:pl-4 border-zinc-200 dark:border-zinc-800' }}">
                {{ $description }}
            </p>
        @endif
    </div>
    
    @if($action)
        <div class="flex-shrink-0">
            {{ $action }}
        </div>
    @endif
</div>

@push('head')
    <title>{{ $title }} - {{ ucwords(config('app.name')) }}</title>
@endpush
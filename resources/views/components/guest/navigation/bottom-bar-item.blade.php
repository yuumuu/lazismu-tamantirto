@props(['route', 'icon'])

<a href="{{ Route::has($route) ? route($route) : '#' }}"
    {{ $attributes->merge([
        'class' =>
            'flex flex-col items-center gap-1 py-2 px-4 min-w-22 rounded-full transition-colors ' .
            navigationLinkClass('bottomBar', $route),
    ]) }}>
    <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
    <span class="text-[10px] font-medium">{{ $slot }}</span>
</a>

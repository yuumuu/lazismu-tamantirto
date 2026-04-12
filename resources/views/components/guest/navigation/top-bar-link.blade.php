@props(['route'])

@php
    $isActive = request()->routeIs($route.'*');
    $activeStyle = 'border-radius: 14px 6px 14px 6px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1), inset 0 1px 0 0 rgb(255 255 255 / 0.1);';
@endphp

<a href="{{ Route::has($route) ? route($route) : '#' }}"
    @class([
        'relative z-10 px-5 py-2.5 text-sm font-bold transition-all duration-300 whitespace-nowrap cursor-pointer',
        'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-lg' => $isActive,
        'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-zinc-800/60 rounded-xl' => !$isActive,
    ])
    {!! $isActive ? "style=\"{$activeStyle}\"" : '' !!}>
    {{ $slot }}
</a>

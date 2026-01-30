@props(['route'])

<a href="{{ Route::has($route) ? route($route) : '#' }}"
    {{ $attributes->merge([
        'class' =>
            'rounded-lg px-5 py-3 sm:px-3 sm:py-2 text-lg sm:text-sm font-medium transition-colors mb-0 ' .
            navigationLinkClass('topBar', $route),
    ]) }}>
    {{ $slot }}
</a>

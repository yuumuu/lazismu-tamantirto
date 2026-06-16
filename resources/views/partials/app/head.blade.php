<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="user-role" content="{{ auth()->check() ? (auth()->user()->role?->value ?? 'viewer') : 'guest' }}" />

<title>{{ $title ?? 'Dashboard' }} - {{ ucwords(config('app.name')) }}</title>
@stack('head')

<link rel="icon" href="/favicon.png?t={{ time() }}" type="image/png">
<link rel="icon" href="/favicon.ico?t={{ time() }}" sizes="any">
<link rel="apple-touch-icon" href="/favicon.png?t={{ time() }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|plus-jakarta-sans:300,400,500,600,700,800|fira-code:400,500,600,700" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

<!-- Quill Editor -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<style>
    .ql-editor { min-height: 15rem; font-size: 1rem; }
    .ql-container.ql-snow { border-bottom-left-radius: 0.75rem; border-bottom-right-radius: 0.75rem; }
    .ql-toolbar.ql-snow { border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem; }
</style>

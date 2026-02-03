<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="user-role" content="{{ auth()->check() ? (auth()->user()->roles->first()?->name ?? 'viewer') : 'guest' }}" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.png?t={{ time() }}" type="image/png">
<link rel="icon" href="/favicon.ico?t={{ time() }}" sizes="any">
<link rel="apple-touch-icon" href="/favicon.png?t={{ time() }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:300,400,500,600,700,800|fira-code:400,500,600,700"
    rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/guest.js'])
@fluxAppearance
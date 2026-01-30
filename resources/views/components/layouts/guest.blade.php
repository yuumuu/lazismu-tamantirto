<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    @include('partials.guest.head')
</head>

<body x-data="{ open: false }" class="font-sans min-h-screen bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased selection:bg-primary/20 selection:text-primary">
    
    @include('partials.guest.navbar')
    @include('partials.guest.mobile-menu')

    <main>
        {{ $slot }}
    </main>

    @include('partials.guest.footer')
    @include('partials.guest.bottom-nav')

    @fluxScripts
</body>

</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.app.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-zinc-900">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <!-- Branding Side (Left) -->
            <div class="relative hidden h-full flex-col bg-primary p-10 text-white lg:flex dark:border-r dark:border-zinc-800 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-primary to-primary-dark opacity-90"></div>
                <div class="absolute -bottom-24 -left-24 size-96 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute top-24 -right-24 size-64 rounded-full bg-white/10 blur-3xl"></div>
                
                <a href="{{ route('guest.home') }}" class="relative z-20 flex items-center gap-3 text-lg font-medium" wire:navigate>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-primary font-black text-xl shadow-lg">
                        L
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="font-black tracking-tight text-xl">LAZISMU</span>
                        <span class="text-xs font-medium text-white/80">Tamantirto</span>
                    </div>
                </a>

                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                <div class="relative z-20 mt-auto">
                    <blockquote class="space-y-4 max-w-lg">
                        <p class="text-xl font-medium">
                            &ldquo;{{ trim($message) }}&rdquo;
                        </p>
                        <footer class="text-sm font-bold opacity-80">&mdash; {{ trim($author) }}</footer>
                    </blockquote>
                </div>
            </div>

            <!-- Form Side (Right) -->
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[400px]">
                    <a href="{{ route('guest.home') }}" class="z-20 flex items-center justify-center gap-2 font-medium lg:hidden mb-8" wire:navigate>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white font-black text-xl shadow-lg">
                            L
                        </div>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>

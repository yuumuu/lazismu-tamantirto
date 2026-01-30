@php
    $donation = \App\Models\Donation::findOrFail(request('id'));
@endphp

<x-layouts.guest>
    <div class="py-12 md:py-24 bg-zinc-50 dark:bg-zinc-950 min-h-[calc(100vh-80px)]">
        <div class="mx-auto max-w-2xl px-4 md:px-0">
            @livewire('guest.upload-proof', ['donation' => $donation])
        </div>
    </div>
</x-layouts.guest>

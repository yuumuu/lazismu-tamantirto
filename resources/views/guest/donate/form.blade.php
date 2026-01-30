<x-layouts.guest>
    <div class="py-12 md:py-24 bg-zinc-50 dark:bg-zinc-950">
        <div class="mx-auto max-w-4xl px-4 md:px-0">
            @livewire('guest.donate-wizard', ['campaign_slug' => request('campaign_slug')])
        </div>
    </div>
</x-layouts.guest>

<div class="flex items-start max-md:flex-col p-6 pt-0 lg:pt-0 lg:p-10">
    <div class="me-10 w-full md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
            <flux:navlist.item :href="route('user-password.edit')" wire:navigate>{{ __('Password') }}</flux:navlist.item>
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <flux:navlist.item :href="route('two-factor.show')" wire:navigate>{{ __('Two-Factor Auth') }}</flux:navlist.item>
            @endif
            <flux:navlist.item :href="route('appearance.edit')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <div class="premium-card p-10">
            <div class="space-y-1 mb-8">
                 <h3 class="text-xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $heading ?? '' }}</h3>
                 <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400 border-l pl-4 border-zinc-100 dark:border-zinc-800">{{ $subheading ?? '' }}</p>
            </div>

            <div class="w-full">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

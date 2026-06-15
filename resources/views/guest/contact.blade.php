<x-layouts.guest title="Kontak">
    <div class="py-12 md:py-24 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-start">
                <!-- Info Section -->
                <div class="space-y-12">
                    <div class="space-y-6">
                        <h2 class="text-primary font-black uppercase text-xs">Hubungi Kami</h2>
                        <h1
                            class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                            Selalu Siap Mendengar<br>& Melayani Anda</h1>
                        <p class="text-lg text-zinc-500 font-medium">
                            Punya pertanyaan seputar zakat atau ingin berkolaborasi? Jangan ragu untuk menghubungi kami
                            melalui saluran di bawah ini.
                        </p>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center gap-6 group">
                            <div
                                class="size-16 rounded-[24px] bg-white dark:bg-zinc-900 shadow-xl border border-zinc-100 dark:border-white/5 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all">
                                <flux:icon.map-pin class="size-7" />
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-zinc-400 uppercase tracking-widest mb-1">Alamat
                                    Kantor</h4>
                                <p class="font-bold text-zinc-900 dark:text-white">{{ setting('contact_address') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6 group">
                            <div
                                class="size-16 rounded-[24px] bg-white dark:bg-zinc-900 shadow-xl border border-zinc-100 dark:border-white/5 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all">
                                <flux:icon.phone class="size-7" />
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-zinc-400 uppercase tracking-widest mb-1">Telepon &
                                    WhatsApp</h4>
                                <p class="font-bold text-zinc-900 dark:text-white">{{ setting('contact_phone') }} /
                                    {{ setting('contact_whatsapp') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6 group">
                            <div
                                class="size-16 rounded-[24px] bg-white dark:bg-zinc-900 shadow-xl border border-zinc-100 dark:border-white/5 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all">
                                <flux:icon.envelope class="size-7" />
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-zinc-400 uppercase tracking-widest mb-1">Email Resmi
                                </h4>
                                <p class="font-bold text-zinc-900 dark:text-white">{{ setting('contact_email') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Icons -->
                    <div class="pt-12 border-t border-zinc-200 dark:border-white/5 space-y-6">
                        <h4 class="text-xs font-black text-zinc-400 uppercase tracking-widest">Ikuti Media Sosial</h4>
                        <div class="flex gap-4">
                            @foreach (['facebook', 'instagram', 'twitter', 'youtube'] as $sm)
                                <a href="{{ setting('social_' . $sm) }}"
                                    class="size-12 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-white/5 shadow-sm flex items-center justify-center text-zinc-400 hover:text-primary hover:border-primary transition-all">
                                    <flux:icon name="{{ $sm }}" class="size-6" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Map Section -->
                <div class="space-y-8 lg:sticky lg:top-24">
                    <div
                        class="bg-white dark:bg-zinc-900 p-4 rounded-[48px] border border-zinc-200 dark:border-white/5 shadow-2xl">
                        <div
                            class="w-full aspect-[4/5] rounded-[36px] overflow-hidden grayscale contrast-125 dark:invert dark:opacity-80">
                            <!-- Placeholder for Google Maps iframe -->
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15810.835497232338!2d110.32049685!3d-7.82062535!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7af80302b11545%3A0xe549646b142921fd!2zTFFSVlVRIChMYXppc01VKSBUYW1hbnRpcnRv!5e0!3m2!1sid!2sid!4v1711234567890"
                                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>

                    <flux:button href="{{ setting('contact_maps_url') }}" variant="ghost"
                        class="w-full h-16 rounded-2xl font-black border-zinc-200 dark:border-white/10"
                        icon-trailing="arrow-top-right-on-square">
                        Buka di Google Maps
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>

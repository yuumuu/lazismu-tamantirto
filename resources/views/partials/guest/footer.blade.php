<footer class="bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-white/5 pt-16 pb-32">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            <!-- Branding -->
            <div class="space-y-6">
                <a href="{{ route('guest.home') }}" class="flex items-center gap-3">
                    <div class="flex aspect-square size-10 items-center justify-center rounded-xl bg-transparent text-white shadow-none">
                        <x-app-logo-icon class="size-8 fill-current" />
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-zinc-900 dark:text-white font-black text-xl tracking-tighter uppercase">Lazismu</span>
                        <span class="text-primary font-bold text-[10px] tracking-[0.2em] uppercase">Tamantirto</span>
                    </div>
                </a>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed">
                    Memberi untuk negeri. Lazismu Tamantirto berkomitmen untuk mengelola zakat, infaq dan sedekah secara profesional dan amanah.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="p-2 rounded-lg bg-white dark:bg-white/5 border border-zinc-200 dark:border-white/10 text-zinc-600 dark:text-zinc-400 hover:text-primary transition-colors">
                        <flux:icon.facebook class="size-5" />
                    </a>
                    <a href="#" class="p-2 rounded-lg bg-white dark:bg-white/5 border border-zinc-200 dark:border-white/10 text-zinc-600 dark:text-zinc-400 hover:text-primary transition-colors">
                        <flux:icon.instagram class="size-5" />
                    </a>
                    <a href="#" class="p-2 rounded-lg bg-white dark:bg-white/5 border border-zinc-200 dark:border-white/10 text-zinc-600 dark:text-zinc-400 hover:text-primary transition-colors">
                        <flux:icon.twitter class="size-5" />
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-zinc-900 dark:text-white font-bold mb-6">Tautan Cepat</h3>
                <ul class="space-y-4">
                    <li><a href="{{ route('guest.home') }}" class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm">Beranda</a></li>
                    <li><a href="{{ route('guest.campaigns.index') }}" class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm">Program Donasi</a></li>
                    <li><a href="{{ route('guest.posts.index') }}" class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm">Berita & Artikel</a></li>
                    <li><a href="{{ route('guest.about') }}" class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm">Tentang Kami</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div>
                <h3 class="text-zinc-900 dark:text-white font-bold mb-6">Layanan</h3>
                <ul class="space-y-4">
                    <li><a href="{{ route('guest.donate.form') }}" class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm">Zakat Online</a></li>
                    <li><a href="{{ route('guest.donate.form') }}" class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm">Infaq & Sedekah</a></li>
                    <li><a href="{{ route('guest.calculator') }}" class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm">Kalkulator Zakat</a></li>
                    <li><a href="{{ route('guest.reports') }}" class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm">Laporan Keuangan</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-zinc-900 dark:text-white font-bold mb-6">Kontak Kami</h3>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <flux:icon.map-pin class="size-5 text-primary shrink-0" />
                        <span class="text-zinc-500 dark:text-zinc-400 text-sm italic">Jl. Brawijaya, Tamantirto, Kasihan, Bantul, DIY</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <flux:icon.phone class="size-5 text-primary shrink-0" />
                        <span class="text-zinc-500 dark:text-zinc-400 text-sm">0812-3456-7890</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <flux:icon.envelope class="size-5 text-primary shrink-0" />
                        <span class="text-zinc-500 dark:text-zinc-400 text-sm">lazismu@umy.ac.id</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-16 pt-8 border-t border-zinc-200 dark:border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-zinc-400 dark:text-zinc-500 text-xs">
                &copy; {{ date('Y') }} Lazismu Tamantirto. All rights reserved.
            </p>
            <div class="flex gap-6">
                <a href="#" class="text-zinc-400 dark:text-zinc-500 hover:text-primary text-xs transition-colors tracking-tight">Kebijakan Privasi</a>
                <a href="#" class="text-zinc-400 dark:text-zinc-500 hover:text-primary text-xs transition-colors tracking-tight">Syarat & Ketentuan</a>
            </div>
        </div>
    </div>
</footer>

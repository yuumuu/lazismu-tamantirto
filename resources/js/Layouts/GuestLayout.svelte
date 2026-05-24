<script>
    import { Link } from '@inertiajs/svelte';
</script>

<div class="min-h-screen flex flex-col bg-gray-50">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <Link href="/" class="flex items-center">
                        <span class="text-2xl font-bold text-primary-600">Lazismu</span>
                    </Link>
                </div>
                <div class="flex items-center space-x-4">
                    <Link href="/program" class="text-gray-700 hover:text-primary-600 font-medium">Program</Link>
                    <Link href="/berita" class="text-gray-700 hover:text-primary-600 font-medium">Berita</Link>
                    <Link href="/admin" class="text-gray-700 hover:text-primary-600 font-medium">Login</Link>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        <slot />
    </main>

    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; {new Date().getFullYear()} Lazismu. All rights reserved.</p>
        </div>
    </footer>
</div>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Segera Hadir - LAZISMU Tamantirto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .bg-primary {
            background-color: #f97316;
        }

        .text-primary {
            color: #f97316;
        }

        .border-primary {
            border-color: #f97316;
        }
    </style>
</head>

<body class="bg-zinc-950 flex items-center justify-center min-h-screen p-6 overflow-hidden relative">
    <!-- Animated background patterns -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-10%] left-[-10%] size-[400px] bg-primary/20 rounded-full blur-[120px] animate-pulse">
        </div>
        <div class="absolute bottom-[-10%] right-[-10%] size-[400px] bg-primary/10 rounded-full blur-[120px] animate-pulse"
            style="animation-delay: 2s"></div>
    </div>

    <div class="max-w-4xl w-full text-center space-y-12 relative z-10">
        <!-- Badge -->
        <div
            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-md">
            <span class="size-2 rounded-full bg-primary animate-ping"></span>
            <span class="text-xs font-black text-white uppercase">Coming Soon</span>
        </div>

        <!-- Headline -->
        <div class="space-y-6">
            <h1 class="text-5xl md:text-8xl font-black text-white tracking-tighter leading-[0.9]">Sesuatu yang<br><span
                    class="text-primary italic">Besar</span> Segera Hadir.</h1>
            <p class="text-lg md:text-2xl text-zinc-400 font-medium max-w-2xl mx-auto">
                Kami sedang menyiapkan pengalaman baru dalam berdonasi yang lebih mudah, transparan, dan berdampak bagi
                sesama.
            </p>
        </div>

        <!-- Timer Placeholder (Statik untuk demonstrasi) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8 max-w-2xl mx-auto">
            <div class="bg-white/5 border border-white/10 backdrop-blur-xl p-6 rounded-3xl space-y-2">
                <span class="text-4xl font-black text-white">05</span>
                <span class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Hari</span>
            </div>
            <div class="bg-white/5 border border-white/10 backdrop-blur-xl p-6 rounded-3xl space-y-2">
                <span class="text-4xl font-black text-white">12</span>
                <span class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Jam</span>
            </div>
            <div class="bg-white/5 border border-white/10 backdrop-blur-xl p-6 rounded-3xl space-y-2">
                <span class="text-4xl font-black text-white">45</span>
                <span class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Menit</span>
            </div>
            <div class="bg-white/5 border border-white/10 backdrop-blur-xl p-6 rounded-3xl space-y-2">
                <span class="text-4xl font-black text-white">20</span>
                <span class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Detik</span>
            </div>
        </div>

        <!-- CTA / Subscribe -->
        <div class="max-w-md mx-auto space-y-4">
            <div class="relative group">
                <input type="email" placeholder="Masukkan email Anda..."
                    class="w-full h-16 bg-white/5 border border-white/10 rounded-2xl px-6 text-white focus:outline-none focus:border-primary/50 transition-all">
                <button
                    class="absolute right-2 top-2 h-12 px-6 bg-primary text-white font-black text-xs uppercase tracking-widest rounded-xl hover:scale-105 active:scale-95 transition-all">
                    Ingatkan Saya
                </button>
            </div>
            <p class="text-[10px] text-zinc-600 font-medium italic">Dapatkan notifikasi saat kami resmi meluncur.</p>
        </div>
    </div>
</body>

</html>

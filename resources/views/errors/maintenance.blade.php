<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - LAZISMU Tamantirto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-primary { background-color: #f97316; }
        .text-primary { color: #f97316; }
        .border-primary { border-color: #f97316; }
    </style>
</head>
<body class="bg-zinc-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-2xl w-full text-center space-y-12">
        <!-- Logo/Icon -->
        <div class="relative inline-block">
            <div class="size-32 md:size-40 rounded-[40px] bg-white shadow-2xl flex items-center justify-center relative z-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16 md:size-20 text-primary animate-bounce">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.83-5.83m0 0a2.652 2.652 0 11-3.75-3.75 2.652 2.652 0 013.75 3.75zm-4.72-4.72L4.66 4.66a2.652 2.652 0 00-3.75 3.75l5.83 5.83m0 0a2.652 2.652 0 113.75 3.75 2.652 2.652 0 01-3.75-3.75z" />
                </svg>
            </div>
            <div class="absolute -top-4 -right-4 size-12 rounded-2xl bg-primary text-white flex items-center justify-center shadow-lg animate-pulse z-20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
            </div>
        </div>

        <!-- Text Content -->
        <div class="space-y-6">
            <h1 class="text-4xl md:text-6xl font-extrabold text-zinc-900 tracking-tight">Sedang<br><span class="text-primary italic">Ditingkatkan.</span></h1>
            <p class="text-lg md:text-xl text-zinc-500 font-medium max-w-lg mx-auto">
                Mohon maaf atas ketidaknyamanannya. Kami sedang melakukan pemeliharaan infrastruktur untuk memberikan layanan terbaik bagi donatur LAZISMU.
            </p>
        </div>

        <!-- Progress Indicator -->
        <div class="flex flex-col items-center gap-4">
            <div class="w-full max-w-xs h-2 bg-zinc-200 rounded-full overflow-hidden">
                <div class="h-full bg-primary animate-[loading_2s_ease-in-out_infinite]" style="width: 40%"></div>
            </div>
            <span class="text-sm font-bold text-zinc-400 uppercase tracking-widest italic">Optimasi Database & UI...</span>
        </div>

        <!-- Social/Contact -->
        <div class="pt-12 border-t border-zinc-200 flex flex-col md:flex-row items-center justify-center gap-8">
            <div class="flex items-center gap-3 text-zinc-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                <span class="font-bold">lazismu@tamantirto.org</span>
            </div>
            <div class="flex items-center gap-3 text-zinc-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                </svg>
                <span class="font-bold">0812-3456-7890</span>
            </div>
        </div>
    </div>

    <style>
        @keyframes loading {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(250%); }
        }
    </style>
</body>
</html>

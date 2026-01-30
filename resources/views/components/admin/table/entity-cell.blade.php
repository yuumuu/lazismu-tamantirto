<div class="flex items-center gap-4">
    <div class="size-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400 font-bold text-xs group-hover:bg-primary group-hover:text-white transition-all duration-300">
        {{ substr($name, 0, 1) }}
    </div>
    <div class="flex flex-col">
        <span class="font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors">{{ $name }}</span>
        <span class="text-[10px] text-zinc-400 font-mono">{{ $meta }}</span>
    </div>
</div>

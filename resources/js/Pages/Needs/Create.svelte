<script>
    import { useForm } from '@inertiajs/svelte';
    import { Link } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';

    let {
        categories = [],
    } = $props();

    let form = useForm({
        applicant_name: '',
        applicant_phone: '',
        applicant_address: '',
        applicant_email: '',
        title: '',
        description: '',
        category: '',
        amount_requested: '',
        attachment: null,
    });

    let errors = $state({});
    let fileName = $state('');

    const categoryOptions = [
        { value: 'health', label: 'Kesehatan' },
        { value: 'education', label: 'Pendidikan' },
        { value: 'business', label: 'Modal Usaha' },
        { value: 'basic_needs', label: 'Kebutuhan Pokok' },
        { value: 'other', label: 'Lainnya' },
    ];

    function handleFileSelect(e) {
        const file = e.target?.files?.[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            errors.attachment = 'File maksimal 2MB';
            e.target.value = '';
            return;
        }
        form.attachment = file;
        fileName = file.name;
        errors.attachment = null;
    }

    function removeFile() {
        form.attachment = null;
        fileName = '';
    }

    function validate() {
        const errs = {};
        if (!form.applicant_name?.trim()) errs.applicant_name = 'Nama lengkap wajib diisi';
        if (!form.applicant_phone?.trim()) errs.applicant_phone = 'Nomor WhatsApp wajib diisi';
        else if (!/^0\d{9,12}$/.test(form.applicant_phone.replace(/[\s-]/g, ''))) errs.applicant_phone = 'Format nomor tidak valid (mulai dengan 0, 10-13 digit)';
        if (!form.applicant_address?.trim()) errs.applicant_address = 'Alamat wajib diisi';
        if (form.applicant_email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.applicant_email)) errs.applicant_email = 'Format email tidak valid';
        if (!form.title?.trim()) errs.title = 'Judul pengajuan wajib diisi';
        if (!form.description?.trim()) errs.description = 'Deskripsi wajib diisi';
        if (!form.category) errs.category = 'Kategori wajib dipilih';
        if (!form.amount_requested || Number(form.amount_requested) <= 0) errs.amount_requested = 'Jumlah yang dimohon wajib diisi';
        errors = errs;
        return Object.keys(errs).length === 0;
    }

    function submit() {
        if (!validate()) return;
        form.post('/pengajuan', {
            onSuccess: (page) => {
                const token = page.props.flash?.tracking_token || '';
                window.location.href = `/pengajuan/sukses?token=${token}`;
            },
        });
    }
</script>

<svelte:head>
    <title>Ajukan Permohonan | Lazismu</title>
</svelte:head>

<Layout>
    <div class="py-12 md:py-24 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
        <div class="mx-auto max-w-4xl px-6 lg:px-8">
            <div class="space-y-8">
                <!-- Header -->
                <div class="text-center space-y-4">
                    <h1 class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight">Ajukan Permohonan</h1>
                    <p class="text-lg text-zinc-500 font-medium max-w-2xl mx-auto">
                        Lengkapi formulir di bawah untuk mengajukan permohonan bantuan
                    </p>
                </div>

                <!-- Form Card -->
                <div class="bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5 shadow-2xl p-8 md:p-12">
                    <form onsubmit={(e) => { e.preventDefault(); submit(); }} class="space-y-10">
                        <!-- Data Pemohon Section -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-4 pb-4 border-b border-zinc-100 dark:border-white/5">
                                <div class="size-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                    <svg class="size-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </div>
                                <h2 class="text-xl font-black text-zinc-900 dark:text-white">Data Pemohon</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="applicant_name" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input id="applicant_name" type="text" bind:value={form.applicant_name} placeholder="Masukkan nama lengkap..."
                                        class="w-full h-14 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 font-bold text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                    />
                                    {#if errors.applicant_name}<p class="text-xs text-red-500 font-bold">{errors.applicant_name}</p>{/if}
                                </div>
                                <div class="space-y-2">
                                    <label for="applicant_phone" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Nomor WhatsApp <span class="text-red-500">*</span></label>
                                    <input id="applicant_phone" type="tel" bind:value={form.applicant_phone} placeholder="Contoh: 08123456789"
                                        class="w-full h-14 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 font-bold text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                    />
                                    {#if errors.applicant_phone}<p class="text-xs text-red-500 font-bold">{errors.applicant_phone}</p>{/if}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="applicant_address" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Alamat Lengkap <span class="text-red-500">*</span></label>
                                <textarea id="applicant_address" bind:value={form.applicant_address} rows="3" placeholder="Masukkan alamat lengkap..."
                                    class="w-full rounded-2xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                ></textarea>
                                {#if errors.applicant_address}<p class="text-xs text-red-500 font-bold">{errors.applicant_address}</p>{/if}
                            </div>

                            <div class="space-y-2">
                                <label for="applicant_email" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Email (Opsional)</label>
                                <input id="applicant_email" type="email" bind:value={form.applicant_email} placeholder="nama@email.com"
                                    class="w-full h-14 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 font-bold text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                />
                                {#if errors.applicant_email}<p class="text-xs text-red-500 font-bold">{errors.applicant_email}</p>{/if}
                            </div>
                        </div>

                        <!-- Detail Pengajuan Section -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-4 pb-4 border-b border-zinc-100 dark:border-white/5">
                                <div class="size-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                    <svg class="size-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <h2 class="text-xl font-black text-zinc-900 dark:text-white">Detail Pengajuan</h2>
                            </div>

                            <div class="space-y-2">
                                <label for="need_title" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Judul Pengajuan <span class="text-red-500">*</span></label>
                                <input id="need_title" type="text" bind:value={form.title} placeholder="Contoh: Bantuan Biaya Pengobatan"
                                    class="w-full h-14 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 font-bold text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                />
                                {#if errors.title}<p class="text-xs text-red-500 font-bold">{errors.title}</p>{/if}
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="need_category" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Kategori <span class="text-red-500">*</span></label>
                                    <select id="need_category" bind:value={form.category}
                                        class="w-full h-14 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 font-bold text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                    >
                                        <option value="">Pilih kategori...</option>
                                        {#each categoryOptions as cat}
                                            <option value={cat.value}>{cat.label}</option>
                                        {/each}
                                    </select>
                                    {#if errors.category}<p class="text-xs text-red-500 font-bold">{errors.category}</p>{/if}
                                </div>
                                <div class="space-y-2">
                                    <label for="amount_requested" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Jumlah yang Dimohon (Rp) <span class="text-red-500">*</span></label>
                                    <input id="amount_requested" type="number" bind:value={form.amount_requested} min="0" step="1000" placeholder="Masukkan nominal..."
                                        class="w-full h-14 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 font-bold text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                    />
                                    {#if errors.amount_requested}<p class="text-xs text-red-500 font-bold">{errors.amount_requested}</p>{/if}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="need_description" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Deskripsi <span class="text-red-500">*</span></label>
                                <textarea id="need_description" bind:value={form.description} rows="5" placeholder="Jelaskan secara detail permohonan Anda..."
                                    class="w-full rounded-2xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                ></textarea>
                                {#if errors.description}<p class="text-xs text-red-500 font-bold">{errors.description}</p>{/if}
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-black text-zinc-400 uppercase tracking-widest">Lampiran (Opsional)</label>
                                {#if fileName}
                                    <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3">
                                            <svg class="size-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            <span class="text-sm font-bold text-zinc-900 dark:text-white">{fileName}</span>
                                        </div>
                                        <button type="button" onclick={removeFile}
                                            class="text-xs text-red-500 font-bold hover:underline cursor-pointer"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                {:else}
                                    <label class="flex items-center justify-center w-full h-20 rounded-2xl border-2 border-dashed border-zinc-200 dark:border-white/5 hover:border-primary transition-all cursor-pointer bg-zinc-50 dark:bg-zinc-950">
                                        <div class="flex items-center gap-3">
                                            <svg class="size-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                            <span class="text-sm font-bold text-zinc-500">Klik untuk unggah (JPG/PNG/PDF, maks 2MB)</span>
                                        </div>
                                        <input type="file" onchange={handleFileSelect} class="hidden" accept=".jpg,.jpeg,.png,.pdf" />
                                    </label>
                                {/if}
                                {#if errors.attachment}<p class="text-xs text-red-500 font-bold">{errors.attachment}</p>{/if}
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" disabled={form.processing}
                                class="w-full h-16 rounded-xl bg-primary text-white font-black uppercase tracking-widest shadow-xl shadow-primary/30 hover:bg-primary/90 transition-all disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
                            >
                                {#if form.processing}
                                    <svg class="size-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    Memproses...
                                {:else}
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    Kirim Pengajuan
                                {/if}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Info Card -->
                <div class="bg-gradient-to-br from-primary/5 to-primary/10 dark:from-primary/10 dark:to-primary/20 rounded-[40px] border border-primary/20 p-8 md:p-12">
                    <div class="flex items-start gap-4">
                        <div class="size-12 rounded-xl bg-primary/20 flex items-center justify-center shrink-0">
                            <svg class="size-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-zinc-900 dark:text-white mb-2">Informasi Penting</h3>
                            <ul class="space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                                <li>• Setelah mengirim, Anda akan mendapatkan token unik untuk melacak status pengajuan.</li>
                                <li>• Tim kami akan memproses pengajuan dan menghubungi Anda melalui WhatsApp.</li>
                                <li>• Pastikan nomor WhatsApp yang didaftarkan aktif.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</Layout>

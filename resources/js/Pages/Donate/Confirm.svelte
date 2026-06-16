<script>
    import { onMount } from 'svelte';
    import { router } from '@inertiajs/svelte';

    onMount(() => {
        router.visit('/donasi');
    });
</script>

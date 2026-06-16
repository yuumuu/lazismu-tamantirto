import { createInertiaApp } from '@inertiajs/svelte';
import { mount } from 'svelte';
import '../css/app.css';

const scriptPage = document.querySelector('script[data-page="app"][type="application/json"]');
const divPage = document.getElementById('app')?.dataset.page;
const page = scriptPage
    ? JSON.parse(scriptPage.textContent)
    : divPage
        ? JSON.parse(divPage)
        : undefined;

createInertiaApp({
  page,
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.svelte');
    return pages[`./Pages/${name}.svelte`]();
  },
  setup({ el, App, props }) {
    mount(App, { target: el, props });
  },
});
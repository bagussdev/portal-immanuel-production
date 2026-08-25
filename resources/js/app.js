import './bootstrap';

import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

window.Alpine = Alpine;
window.Sortable = Sortable;

const themeApi = {
    get() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    },
    set(theme) {
        const normalized = theme === 'dark' ? 'dark' : 'light';
        document.documentElement.classList.toggle('dark', normalized === 'dark');
        document.documentElement.dataset.theme = normalized;
        localStorage.setItem('ip-theme', normalized);
        document.querySelector('meta[name="theme-color"]')?.setAttribute(
            'content',
            normalized === 'dark' ? '#080b12' : '#f0f9ff',
        );
        window.dispatchEvent(new CustomEvent('ip-theme-changed', { detail: normalized }));
        return normalized;
    },
    toggle() {
        return this.set(this.get() === 'dark' ? 'light' : 'dark');
    },
};

window.ImmanuelTheme = themeApi;

Alpine.data('appShell', (initial = {}) => ({
    sidebarOpen: false,
    theme: themeApi.get(),
    ...initial,
    init() {
        this.$watch('sidebarOpen', (open) => {
            if (window.innerWidth < 1024) document.body.classList.toggle('overflow-hidden', open);
        });
        window.addEventListener('ip-theme-changed', (event) => { this.theme = event.detail; });
    },
    toggleTheme() {
        this.theme = themeApi.toggle();
    },
    closeSidebar() {
        this.sidebarOpen = false;
        document.body.classList.remove('overflow-hidden');
    },
}));

function setupResponsiveTable(wrapper) {
    if (wrapper.dataset.scrollReady === 'true') return;

    const table = wrapper.querySelector('table');
    if (!table) return;

    wrapper.dataset.scrollReady = 'true';
    wrapper.classList.add('ip-table-scroller');

    const controls = document.createElement('div');
    controls.className = 'ip-table-controls';
    controls.setAttribute('aria-label', 'Navigasi tabel');
    controls.innerHTML = `
        <span class="ip-table-hint">Geser tabel</span>
        <button type="button" class="ip-table-arrow" data-direction="left" aria-label="Geser tabel ke kiri">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>
        </button>
        <button type="button" class="ip-table-arrow" data-direction="right" aria-label="Geser tabel ke kanan">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
        </button>`;

    wrapper.parentElement?.insertBefore(controls, wrapper);
    const [left, right] = controls.querySelectorAll('button');

    const update = () => {
        const overflow = wrapper.scrollWidth > wrapper.clientWidth + 2;
        controls.classList.toggle('is-hidden', !overflow);
        left.disabled = wrapper.scrollLeft <= 2;
        right.disabled = wrapper.scrollLeft + wrapper.clientWidth >= wrapper.scrollWidth - 2;
    };

    controls.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-direction]');
        if (!button) return;
        const direction = button.dataset.direction === 'left' ? -1 : 1;
        wrapper.scrollBy({ left: direction * Math.max(260, wrapper.clientWidth * 0.72), behavior: 'smooth' });
    });

    wrapper.addEventListener('scroll', update, { passive: true });
    new ResizeObserver(update).observe(wrapper);
    update();
}

function setupResponsiveTables(root = document) {
    root.querySelectorAll('main .ip-table-wrap, main .overflow-x-auto').forEach(setupResponsiveTable);
}

function setupResponsiveDisclosure(disclosure) {
    if (disclosure.dataset.disclosureReady === 'true') return;

    disclosure.dataset.disclosureReady = 'true';
    const desktop = window.matchMedia('(min-width: 768px)');
    const mobileDefault = disclosure.dataset.mobileOpen === 'true';
    let syncing = false;

    const setOpen = (open) => {
        syncing = true;
        disclosure.open = open;
        queueMicrotask(() => { syncing = false; });
    };

    const sync = () => {
        if (desktop.matches) {
            setOpen(true);
        } else if (disclosure.dataset.mobileToggled !== 'true') {
            setOpen(mobileDefault);
        }
    };

    disclosure.addEventListener('toggle', () => {
        if (!syncing && !desktop.matches) disclosure.dataset.mobileToggled = 'true';
    });
    desktop.addEventListener('change', sync);
    sync();
}

function setupResponsiveDisclosures(root = document) {
    root.querySelectorAll('[data-responsive-disclosure]').forEach(setupResponsiveDisclosure);
}

document.addEventListener('DOMContentLoaded', () => {
    setupResponsiveTables();
    setupResponsiveDisclosures();
});
window.addEventListener('load', () => {
    setupResponsiveTables();
    setupResponsiveDisclosures();
});
window.setupResponsiveTables = setupResponsiveTables;
window.setupResponsiveDisclosures = setupResponsiveDisclosures;

Alpine.start();

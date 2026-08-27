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

Alpine.data('userImageEditor', (initial = {}) => {
    const profileTransform = initial.profileTransform || {};
    const ktpTransform = initial.ktpTransform || {};

    return {
        showPassword: false,
        showConfirmation: false,
        identityOpen: window.innerWidth >= 1280,
        profilePreview: initial.profileUrl || null,
        ktpPreview: initial.ktpUrl || null,
        profileImage: null,
        ktpImage: null,
        profileFileName: '',
        ktpFileName: '',
        profileCropX: Number(profileTransform.x ?? 50),
        profileCropY: Number(profileTransform.y ?? 50),
        profileZoom: Number(profileTransform.zoom ?? 1),
        ktpCropX: Number(ktpTransform.x ?? 50),
        ktpCropY: Number(ktpTransform.y ?? 50),
        ktpZoom: Number(ktpTransform.zoom ?? 1),
        ktpRotation: 0,
        profileTransformChanged: false,
        ktpTransformChanged: false,
        init() {
            this.$nextTick(() => {
                if (this.profilePreview) this.loadPreview('profile', this.profilePreview);
                if (this.ktpPreview) this.loadPreview('ktp', this.ktpPreview);
            });
        },
        previewFile(event, kind) {
            const file = event.target.files?.[0];
            if (!file) return;

            const preview = URL.createObjectURL(file);
            if (kind === 'profile') {
                this.profilePreview = preview;
                this.profileFileName = file.name;
                this.profileCropX = 50;
                this.profileCropY = 50;
                this.profileZoom = 1;
                this.profileTransformChanged = true;
            } else {
                this.ktpPreview = preview;
                this.ktpFileName = file.name;
                this.ktpCropX = 50;
                this.ktpCropY = 50;
                this.ktpZoom = 1;
                this.ktpRotation = 0;
                this.ktpTransformChanged = true;
            }
            this.loadPreview(kind, preview);
        },
        loadPreview(kind, source) {
            const image = new Image();
            image.onload = () => {
                this[`${kind}Image`] = image;
                this.$nextTick(() => this.renderPreview(kind));
            };
            image.src = source;
        },
        rotatedSource(image, rotation) {
            if (rotation % 360 === 0) return image;

            const swapSides = rotation % 180 !== 0;
            const canvas = document.createElement('canvas');
            canvas.width = swapSides ? image.naturalHeight : image.naturalWidth;
            canvas.height = swapSides ? image.naturalWidth : image.naturalHeight;
            const context = canvas.getContext('2d');
            context.translate(canvas.width / 2, canvas.height / 2);
            context.rotate((rotation * Math.PI) / 180);
            context.drawImage(image, -image.naturalWidth / 2, -image.naturalHeight / 2);

            return canvas;
        },
        renderPreview(kind) {
            const image = this[`${kind}Image`];
            const canvas = this.$refs[`${kind}Canvas`];
            if (!image || !canvas) return;

            const isProfile = kind === 'profile';
            const outputWidth = isProfile ? 900 : 1284;
            const outputHeight = isProfile ? 900 : 810;
            const rotation = isProfile ? 0 : Number(this.ktpRotation);
            const source = this.rotatedSource(image, rotation);
            const sourceWidth = source.naturalWidth || source.width;
            const sourceHeight = source.naturalHeight || source.height;
            const zoom = Number(this[`${kind}Zoom`]);
            const positionX = Number(this[`${kind}CropX`]) / 100;
            const positionY = Number(this[`${kind}CropY`]) / 100;
            const scale = Math.min(outputWidth / sourceWidth, outputHeight / sourceHeight) * zoom;
            const renderWidth = Math.max(1, Math.round(sourceWidth * scale));
            const renderHeight = Math.max(1, Math.round(sourceHeight * scale));
            const destinationX = Math.round((outputWidth - renderWidth) * positionX);
            const destinationY = Math.round((outputHeight - renderHeight) * positionY);

            canvas.width = outputWidth;
            canvas.height = outputHeight;
            const context = canvas.getContext('2d');
            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, outputWidth, outputHeight);
            context.drawImage(source, destinationX, destinationY, renderWidth, renderHeight);
        },
        rotateKtp(step) {
            this.ktpRotation = (this.ktpRotation + step + 360) % 360;
            this.ktpTransformChanged = true;
            this.$nextTick(() => this.renderPreview('ktp'));
        },
    };
});

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

function setupMoneyInputs(root = document) {
    root.querySelectorAll('[data-money-input]').forEach((input) => {
        if (input.dataset.moneyReady === 'true') return;
        input.dataset.moneyReady = 'true';

        const format = () => {
            const digits = String(input.value || '').replace(/\D/g, '');
            input.value = digits ? new Intl.NumberFormat('id-ID').format(Number(digits)) : '';
        };

        input.addEventListener('input', format);
        format();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setupResponsiveTables();
    setupResponsiveDisclosures();
    setupMoneyInputs();
});
window.addEventListener('load', () => {
    setupResponsiveTables();
    setupResponsiveDisclosures();
    setupMoneyInputs();
});
window.setupResponsiveTables = setupResponsiveTables;
window.setupResponsiveDisclosures = setupResponsiveDisclosures;
window.setupMoneyInputs = setupMoneyInputs;

Alpine.start();

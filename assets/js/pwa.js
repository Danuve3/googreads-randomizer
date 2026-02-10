// PWA registration and install prompt
const PWA = {
    deferredPrompt: null,

    init() {
        this.registerServiceWorker();
        this.setupInstallPrompt();
    },

    async registerServiceWorker() {
        if (!('serviceWorker' in navigator)) return;
        try {
            await navigator.serviceWorker.register('sw.js');
        } catch (err) {
            console.error('SW registration failed:', err);
        }
    },

    setupInstallPrompt() {
        const banner = document.getElementById('pwa-install');
        const installBtn = document.getElementById('pwa-install-btn');
        const dismissBtn = document.getElementById('pwa-dismiss-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;

            // Show banner only if not dismissed before
            if (!localStorage.getItem('gr_pwa_dismissed')) {
                banner.classList.remove('hidden');
            }
        });

        installBtn?.addEventListener('click', async () => {
            if (!this.deferredPrompt) return;
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            this.deferredPrompt = null;
            banner.classList.add('hidden');
            if (outcome === 'accepted') {
                localStorage.setItem('gr_pwa_dismissed', '1');
            }
        });

        dismissBtn?.addEventListener('click', () => {
            banner.classList.add('hidden');
            localStorage.setItem('gr_pwa_dismissed', '1');
        });

        window.addEventListener('appinstalled', () => {
            banner.classList.add('hidden');
            this.deferredPrompt = null;
        });
    },
};

document.addEventListener('DOMContentLoaded', () => {
    PWA.init();
});

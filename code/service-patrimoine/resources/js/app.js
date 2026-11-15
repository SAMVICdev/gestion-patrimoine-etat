import './bootstrap';

// Enregistrement du service worker (mode hors ligne / PWA)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // Le SW est optionnel : l'application reste fonctionnelle sans lui.
        });
    });
}

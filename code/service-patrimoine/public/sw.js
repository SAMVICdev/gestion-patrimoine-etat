/* Service worker — Système d'Information Domaniale
 * Stratégie :
 *  - pages (navigation) : réseau d'abord, repli sur le cache puis /offline.html
 *  - assets statiques (/build, /images, polices) : cache d'abord, réseau en secours
 *  - requêtes API / POST : réseau uniquement (jamais mises en cache)
 */

const CACHE_VERSION = 'patrimoine-v2';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const PAGE_CACHE = `${CACHE_VERSION}-pages`;
const OFFLINE_URL = '/offline.html';

const PRECACHE = [
    OFFLINE_URL,
    '/manifest.webmanifest',
    '/images/blason_rt.png',
    '/images/icon-192.png',
    '/images/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(PRECACHE))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((key) => !key.startsWith(CACHE_VERSION)).map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

function isStaticAsset(url) {
    return url.pathname.startsWith('/build/')
        || url.pathname.startsWith('/images/')
        || url.pathname.startsWith('/fonts/');
}

// Pages contenant des formulaires CSRF : jamais servies depuis le cache,
// sinon un jeton expiré provoque des erreurs 419 à la soumission.
const NO_CACHE_PATHS = [
    '/connexion',
    '/mot-de-passe-oublie',
    '/reinitialiser-mot-de-passe',
    '/deconnexion',
];

function isNoCachePage(url) {
    return NO_CACHE_PATHS.includes(url.pathname);
}

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Ne jamais intercepter les requêtes non-GET (POST constats, etc.)
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) {
        return;
    }

    // Assets statiques : cache d'abord
    if (isStaticAsset(url)) {
        event.respondWith(
            caches.match(request).then((cached) => {
                if (cached) return cached;
                return fetch(request).then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(STATIC_CACHE).then((cache) => cache.put(request, clone));
                    }
                    return response;
                }).catch(() => caches.match(request));
            })
        );
        return;
    }

    // Pages auth : toujours réseau, jamais de cache (jeton CSRF frais requis)
    if (request.mode === 'navigate' && isNoCachePage(url)) {
        return;
    }

    // Pages : réseau d'abord, sinon cache, sinon page hors-ligne
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(PAGE_CACHE).then((cache) => cache.put(request, clone));
                    }
                    return response;
                })
                .catch(async () => {
                    const cached = await caches.match(request);
                    return cached || caches.match(OFFLINE_URL);
                })
        );
    }
});

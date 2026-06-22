const CACHE_NAME = 'pos-offline-v10';

self.addEventListener('install', event => {
    console.log('[SW] Installing v10...');
    self.skipWaiting();

    event.waitUntil((async () => {
        try {
            const cache = await caches.open(CACHE_NAME);

            // 1. Cache Vite manifest assets
            const manifestRes = await fetch('/build/manifest.json');
            if (manifestRes.ok) {
                const manifest = await manifestRes.json();
                const assetUrls = [];
                for (const key in manifest) {
                    const entry = manifest[key];
                    if (entry.file) assetUrls.push('/build/' + entry.file);
                    if (entry.css) entry.css.forEach(f => assetUrls.push('/build/' + f));
                }
                const unique = [...new Set(assetUrls)];
                console.log('[SW] Manifest assets:', unique);
                await Promise.all(unique.map(async url => {
                    try {
                        const res = await fetch(url);
                        if (res.ok) { await cache.put(url, res); console.log('[SW] Cached:', url); }
                    } catch (e) { console.warn('[SW] Failed:', url); }
                }));
            }

            // 2. Cache the /pos page
            try {
                const pageRes = await fetch('/pos', { credentials: 'include', headers: { 'Accept': 'text/html' } });
                if (pageRes.ok) {
                    const pageClone = pageRes.clone();
                    const html = await pageRes.text();
                    await cache.put('/pos', pageClone);
                    console.log('[SW] Cached /pos');

                    // 3. Find and cache livewire.js URL from the HTML
                    // Livewire v3 outputs: /livewire/livewire.js?id=HASH
                    const livewireMatch = html.match(/src="(\/livewire\/livewire\.js[^"]+)"/);
                    if (livewireMatch) {
                        const livewireUrl = livewireMatch[1];
                        console.log('[SW] Found livewire.js:', livewireUrl);
                        try {
                            const lwRes = await fetch(livewireUrl);
                            if (lwRes.ok) {
                                // Store under BOTH the hashed URL AND a generic key
                                await cache.put(livewireUrl, lwRes.clone());
                                await cache.put('/livewire/livewire.js', lwRes.clone());
                                console.log('[SW] Cached livewire.js');
                            }
                        } catch (e) { console.warn('[SW] Failed to cache livewire.js'); }
                    }

                    // 4. Also find bootstrap.js or any other vendor scripts
                    const scriptMatches = html.matchAll(/src="(\/assets\/[^"]+\.js)"/g);
                    for (const m of scriptMatches) {
                        try {
                            const res = await fetch(m[1]);
                            if (res.ok) { await cache.put(m[1], res); console.log('[SW] Cached vendor:', m[1]); }
                        } catch (e) { }
                    }
                }
            } catch (e) { console.warn('[SW] Failed to cache /pos page:', e); }

            // 5. Cache favicon
            try {
                const fav = await fetch('/favicon.ico');
                if (fav.ok) await cache.put('/favicon.ico', fav);
            } catch (e) { }

            console.log('[SW] Install complete!');
        } catch (e) { console.error('[SW] Install failed:', e); }
    })());
});

self.addEventListener('activate', event => {
    console.log('[SW] Activating v10...');
    event.waitUntil(
        caches.keys()
            .then(keys => Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    if (event.request.method !== 'GET') return;
    if (!url.protocol.startsWith('http')) return;
    if (url.port === '5173') return;

    // Skip Livewire UPDATE requests (these POST component state — can't cache)
    // But DO handle livewire.js script file
    if (url.pathname.startsWith('/livewire/update')) return;
    if (url.pathname.startsWith('/livewire/') && !url.pathname.includes('livewire.js')) return;
    if (event.request.headers.get('X-Livewire')) return;

    // Skip POS API
    if (['/pos/sync', '/pos/products/cache', '/pos/products/search',
        '/pos/categories/cache', '/pos/customers/cache', '/pos/customers/create']
        .some(p => url.pathname.startsWith(p))) return;

    event.respondWith(handleFetch(event.request));
});

async function handleFetch(request) {
    const url = new URL(request.url);

    // Cache-first for immutable build assets
    if (url.pathname.startsWith('/build/')) {
        const cached = await caches.match(request);
        if (cached) return cached;
        try {
            const res = await fetch(request);
            if (res.ok) { const c = await caches.open(CACHE_NAME); await c.put(request, res.clone()); }
            return res;
        } catch (e) { return new Response('', { status: 503 }); }
    }

    // Cache-first for livewire.js (it's versioned by ?id= param)
    if (url.pathname.includes('livewire.js')) {
        const cached = await caches.match(request);
        if (cached) { console.log('[SW] Serving livewire.js from cache'); return cached; }
        // Try the generic key too
        const generic = await caches.match('/livewire/livewire.js');
        if (generic) return generic;
        try {
            const res = await fetch(request);
            if (res.ok) { const c = await caches.open(CACHE_NAME); await c.put(request, res.clone()); }
            return res;
        } catch (e) { return new Response('', { status: 503 }); }
    }

    // Cache-first for static assets
    if (url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|webp|svg|ico|woff|woff2|ttf|eot)$/)) {
        const cached = await caches.match(request);
        if (cached) return cached;
        try {
            const res = await fetch(request);
            if (res.ok) { const c = await caches.open(CACHE_NAME); await c.put(request, res.clone()); }
            return res;
        } catch (e) { return new Response('', { status: 503 }); }
    }

    // Network-first for HTML
    try {
        const res = await fetch(request);
        if (res.ok) { const c = await caches.open(CACHE_NAME); await c.put(request, res.clone()); }
        return res;
    } catch (e) {
        const cached = await caches.match(request);
        if (cached) return cached;
        if (request.mode === 'navigate') {
            const fallback = await caches.match('/pos');
            if (fallback) return fallback;
        }
        return new Response('', { status: 503 });
    }
}
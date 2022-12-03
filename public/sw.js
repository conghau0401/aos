self.addEventListener('install', (e) => {
    e.waitUntil(
      caches.open('pohang-store').then((cache) => cache.addAll([
        '/login',
        '/js/build/app.js',
        '/index.js'
      ])),
    );
  });

  self.addEventListener('fetch', (e) => {
    console.log(e.request.url);
    e.respondWith(
      caches.match(e.request).then((response) => response || fetch(e.request)),
    );
  });

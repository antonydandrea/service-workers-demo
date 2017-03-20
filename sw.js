self.addEventListener('install', event => {
    event.waitUntil(
        caches.open('offline-cache').then(function(cache) {
            return cache.addAll([
               'css/main.css',
               'index.php'
            ]);
        })
    );
});


self.addEventListener('activate', event => {

});

self.addEventListener('fetch', event => {
    // request.mode = navigate isn't supported in all browsers
    // so include a check for Accept: text/html header.
    if (event.request.method !== 'POST' && (event.request.mode === 'navigate' || (event.request.method === 'GET' && event.request.headers.get('accept').includes('text/html')))) {
        event.respondWith(
            fetch(event.request.url).catch(error => {
                // Return the offline page
                return caches.match('index.php');
            })
        );
    } else{
        // Respond with everything else if we can
        event.respondWith(caches.match(event.request).then(function (response) {
            return response || fetch(event.request);
            })
        );
    }
});
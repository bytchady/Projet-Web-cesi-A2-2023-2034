self.addEventListener('install', (event) => {
    console.log('Service worker installé');
});

self.addEventListener('activate', (event) => {
    console.log('Service worker activé');
});

self.addEventListener('fetch', (event) => {
    console.log('Requête interceptée:', event.request);
});
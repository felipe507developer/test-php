'use strict';

const CACHE_NAME = 'satatic-cache-v9';

const FILES_TO_CACHE =[
    'index.php',    
    'images/logo.png',    
    'js/install.js'
];

self.addEventListener('install', (evt)=>{
    //console.log('[service worker] Install');
    evt.waitUntil(
        caches.open(CACHE_NAME).then((cache)=>{
            //console.log('pre-caching offline page')
            return cache.addAll(FILES_TO_CACHE);            
        })
    )
    self.skipWaiting();
});

self.addEventListener('activate', (evt) =>{
    //console.log('[serviceWorker] Activate');
    evt.waitUntil(
        caches.keys().then((keyList)=>{
            return Promise.all(keyList.map((key)=>{
                if(key != CACHE_NAME){
                    //console.log('[ServiceWorker] Removing old cache');
                    return caches.delete(key);
                }
            }));
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch',(evt)=>{
    //console.log('[serviceWorker] Fetch',evt.request.url);
    evt.respondWith(
        caches.open(CACHE_NAME).then((cache)=>{
            return cache.match(evt.request).then((response)=>{
                //console.log('RESP',response);
                return response || fetch(evt.request);
            })
        })
    )
});

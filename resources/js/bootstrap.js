import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const reverbMeta = {
    key: document.querySelector('meta[name="reverb-key"]')?.getAttribute('content'),
    host: document.querySelector('meta[name="reverb-host"]')?.getAttribute('content'),
    port: document.querySelector('meta[name="reverb-port"]')?.getAttribute('content'),
    scheme: document.querySelector('meta[name="reverb-scheme"]')?.getAttribute('content'),
};

const reverbKey =
    import.meta.env.VITE_REVERB_APP_KEY ??
    reverbMeta.key ??
    '';

const rawReverbHost =
    import.meta.env.VITE_REVERB_HOST ??
    reverbMeta.host ??
    window.location.hostname;
const rawReverbScheme =
    import.meta.env.VITE_REVERB_SCHEME ??
    reverbMeta.scheme ??
    'http';
const rawReverbPort =
    import.meta.env.VITE_REVERB_PORT ??
    reverbMeta.port ??
    8080;

const reverbHost = String(rawReverbHost)
    .replace(/^[a-z]+:\/\//i, '')
    .replace(/"/g, '')
    .trim();
const reverbScheme = String(rawReverbScheme).replace(/"/g, '').trim().toLowerCase();
const useTls = reverbScheme === 'https';
const reverbPort = Number.parseInt(String(rawReverbPort), 10) || 8080;

if (!reverbKey) {
    console.error('Reverb/Pusher key is missing. Check REVERB_APP_KEY / VITE_REVERB_APP_KEY configuration.');
} else {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: useTls,
        enabledTransports: useTls ? ['wss'] : ['ws'],
    });
}

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const rawReverbHost = import.meta.env.VITE_REVERB_HOST ?? window.location.hostname;
const rawReverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http';

const reverbHost = String(rawReverbHost)
    .replace(/^[a-z]+:\/\//i, '')
    .replace(/"/g, '')
    .trim();
const reverbScheme = String(rawReverbScheme).replace(/"/g, '').trim().toLowerCase();
const useTls = reverbScheme === 'https';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: reverbHost,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: useTls,
    enabledTransports: useTls ? ['wss'] : ['ws'],
});

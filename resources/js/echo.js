import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'kiava-hr-app',
    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Error handling
window.Echo.connector.socket.on('error', (error) => {
    console.error('[Echo] WebSocket Error:', error);
});

window.Echo.connector.socket.on('connect', () => {
    console.log('[Echo] Connected to WebSocket');
});

window.Echo.connector.socket.on('disconnect', () => {
    console.log('[Echo] Disconnected from WebSocket');
});

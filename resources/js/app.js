// Load toast utility first
import './toast.js';

// Load Echo for real-time features
import './echo.js';

// Alpine.js (if needed for simple components)
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

console.log('[Kiava HR] Application initialized with real-time support');

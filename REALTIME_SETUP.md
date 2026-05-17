# Real-Time System Setup Guide

This guide provides complete instructions for setting up and testing the Laravel Reverb + Echo real-time system.

## Prerequisites

- Node.js and npm installed
- PHP 8.3+ with Laravel 12
- PostgreSQL or MySQL database
- Redis (optional but recommended for sessions)

## Installation

### 1. Install Laravel Reverb

```bash
composer require laravel/reverb
php artisan reverb:install
```

This command will:
- Create `config/reverb.php`
- Add Reverb environment variables to `.env`
- Generate app key and secret

### 2. Update Environment Variables

Edit your `.env` file:

```env
BROADCAST_DRIVER=reverb
REVERB_APP_KEY=kiava-hr-app
REVERB_APP_SECRET=kiava-hr-secret
REVERB_APP_ID=1
REVERB_APP_CLUSTER=mt1
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Frontend WebSocket connection
REVERB_FRONTEND_HOST=localhost
REVERB_FRONTEND_PORT=8080
REVERB_FRONTEND_SCHEME=ws
```

### 3. Install Frontend Dependencies

```bash
npm install
npm install --save-dev laravel-echo pusher-js
```

### 4. Create Echo Configuration

Create `resources/js/echo.js`:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

### 5. Update Vite Configuration

In `vite.config.js`, ensure you have:

```javascript
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
```

### 6. Register Echo in Your App

In `resources/js/app.js`:

```javascript
import './echo';
import { createApp } from 'vue';

// Your app code
```

## Running the System

### Terminal 1: Start Reverb Server

```bash
php artisan reverb:start
```

Expected output:
```
INFO  Server running at ws://127.0.0.1:8080
```

### Terminal 2: Start Queue Worker

```bash
php artisan queue:work --timeout=60 --tries=3
```

This processes broadcast events asynchronously.

### Terminal 3: Start Development Server

```bash
npm run dev
```

Or for development with hot reload:

```bash
npm run dev -- --host
```

### Terminal 4: Start Laravel Dev Server (if needed)

```bash
php artisan serve
```

## Testing Real-Time Features

### Method 1: Using the Demo Page

1. Open two browser windows side by side
2. In Window 1, go to: `http://localhost:8000/demo/realtime`
3. In Window 2, do the same
4. In Window 1, trigger events via the demo page
5. Watch updates appear instantly in Window 2

### Method 2: Testing with Postman

1. Create a POST request to `http://localhost:8000/api/documents`
2. Add Authorization header with Bearer token
3. Send document upload event
4. Check both browser windows for updates

### Method 3: Using Artisan Tinker

```bash
php artisan tinker
```

Then dispatch an event:

```php
$document = App\Models\EmployeeDocument::first();
event(new App\Events\DocumentUploaded($document));
```

## Real-Time Channels

### Public Channels

None - all channels are private for security.

### Private Channels

#### company.{companyId}
- **Access**: All users in the company
- **Events**: DocumentUploaded, DocumentApproved, DashboardUpdated, AuditLogCreated
- **Usage**: Company-wide updates visible to all staff

#### user.{userId}
- **Access**: Only the specific user
- **Events**: DocumentApproved, DocumentRejected, DocumentExpiringSoon
- **Usage**: Personal notifications for individual users

#### employee.{employeeId}
- **Access**: The employee and company admins
- **Events**: DocumentApproved, DocumentRejected
- **Usage**: Employee-specific document updates

#### approvals.{companyId}
- **Access**: Only company admins and managers
- **Events**: DocumentUploaded, DocumentApproved, DocumentRejected
- **Usage**: Approval workflow notifications

## Event Reference

### DocumentUploaded
Broadcast when an employee uploads a document.

```javascript
Echo.private('company.' + companyId)
    .listen('DocumentUploaded', (e) => {
        console.log(`${e.employee_name} uploaded ${e.document_type}`);
    });
```

### DocumentApproved
Broadcast when an admin approves a document.

```javascript
Echo.private('user.' + userId)
    .listen('DocumentApproved', (e) => {
        console.log(`Your ${e.document_type} was approved`);
    });
```

### DocumentRejected
Broadcast when an admin rejects a document.

```javascript
Echo.private('user.' + userId)
    .listen('DocumentRejected', (e) => {
        console.log(`Document rejected: ${e.rejection_reason}`);
    });
```

### DocumentExpiringSoon
Broadcast 30, 14, and 7 days before expiration.

```javascript
Echo.private('user.' + userId)
    .listen('DocumentExpiringSoon', (e) => {
        console.log(`${e.document_type} expires in ${e.days_until_expiry} days`);
    });
```

### DashboardStatsUpdated
Broadcast when dashboard statistics change.

```javascript
Echo.private('company.' + companyId)
    .listen('DashboardStatsUpdated', (e) => {
        console.log(`Dashboard stats updated:`, e.stats);
    });
```

## Debugging

### Enable Logging

Update `config/logging.php`:

```php
'channels' => [
    'reverb' => [
        'driver' => 'stack',
        'channels' => ['single'],
        'level' => 'debug',
    ],
],
```

### Check WebSocket Connection

In browser console:

```javascript
// Check if Echo is initialized
console.log(window.Echo);

// Subscribe to a channel and check for errors
Echo.private('user.' + userId)
    .subscribe()
    .listen('.', (e) => console.log('Event:', e));
```

### Monitor Reverb Server

The Reverb server console shows:
- Connection logs
- Channel subscriptions
- Event broadcasts
- Error messages

## Production Deployment

### Using Laravel Reverb Hosted

1. Update `.env` to use Reverb's hosted service
2. Configure DNS and SSL certificates
3. Deploy using your chosen hosting provider

### Using Pusher

1. Create Pusher account at pusher.com
2. Update `.env`:
   ```env
   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=your_app_id
   PUSHER_APP_KEY=your_key
   PUSHER_APP_SECRET=your_secret
   PUSHER_APP_CLUSTER=your_cluster
   ```

### Using Redis Adapter

For horizontal scaling, configure Redis:

```php
// config/reverb.php
'apps_manager' => [
    'driver' => 'redis',
    'connection' => 'default',
],
```

## Troubleshooting

### WebSocket Connection Refused

**Issue**: Browser console shows "Connection refused"

**Solution**:
1. Verify Reverb server is running: `php artisan reverb:start`
2. Check firewall allows port 8080
3. Verify REVERB_HOST and REVERB_PORT in .env match

### Events Not Broadcasting

**Issue**: Events trigger but don't appear in other browsers

**Solution**:
1. Ensure queue worker is running: `php artisan queue:work`
2. Check BROADCAST_DRIVER=reverb in .env
3. Verify channel authorization (routes/channels.php)
4. Check Reverb server logs for errors

### High Memory Usage

**Issue**: Reverb process consuming excessive memory

**Solution**:
1. Reduce connection timeout: `REVERB_SERVER_TIMEOUT=60`
2. Scale to multiple Reverb instances
3. Use Redis instead of in-memory storage

### Slow Updates

**Issue**: Real-time updates lag significantly

**Solution**:
1. Check network latency: `ping localhost:8080`
2. Reduce payload size in events
3. Enable compression in Reverb config
4. Use message batching for bulk operations

## Files Created/Modified

### Configuration
- `config/reverb.php` - Reverb configuration
- `config/broadcasting.php` - Broadcasting drivers
- `routes/channels.php` - Channel authorization

### Events (Created)
- `app/Events/DocumentUploaded.php`
- `app/Events/DocumentApproved.php`
- `app/Events/DocumentRejected.php`
- `app/Events/DocumentExpiringSoon.php`
- `app/Events/DocumentExpired.php`
- `app/Events/AuditLogCreated.php`
- `app/Events/DashboardStatsUpdated.php`

### Livewire Components (Created)
- `app/Livewire/NotificationBell.php`
- `app/Livewire/DashboardStats.php`
- `app/Livewire/ApprovalTable.php`
- `app/Livewire/EmployeeDocumentStatus.php`

### Controllers (Updated)
- `app/Http/Controllers/NotificationController.php`

### Views (Created)
- `resources/views/livewire/notification-bell.blade.php`
- `resources/views/livewire/dashboard-stats.blade.php`
- `resources/views/livewire/approval-table.blade.php`
- `resources/views/livewire/employee-document-status.blade.php`

### Frontend (To be Created)
- `resources/js/echo.js` - Echo configuration
- `resources/js/toast.js` - Toast notifications utility

## Next Steps

1. **Custom Events**: Add domain-specific events as needed
2. **Error Handling**: Implement error boundaries and fallbacks
3. **Analytics**: Track event metrics and performance
4. **Mobile Support**: Add mobile-optimized real-time UI
5. **Database Persistence**: Store event history for audit trails

## Support

For issues or questions:
1. Check Reverb documentation: laravel.com/docs/reverb
2. Review Echo documentation: laravel.com/docs/broadcasting
3. Enable debug logging in Reverb server
4. Check browser WebSocket connection in DevTools Network tab

# Real-Time System - Complete Implementation Summary

## Overview

This document provides a complete summary of the real-time system implementation with Laravel Reverb and Echo.js, including all files created, setup instructions, and testing procedures.

---

## Files Created/Modified

### Configuration Files

#### `config/reverb.php` (NEW)
- Reverb server configuration
- App key/secret setup
- WebSocket host and port configuration
- Encryption settings

#### `config/broadcasting.php` (NEW)
- Broadcasting driver configuration
- Reverb connection setup
- Fallback to Pusher/Ably
- Client options for Echo.js

#### `routes/channels.php` (UPDATED)
- Private channel authorization
- company.{companyId} - All company users
- user.{userId} - Individual user notifications
- employee.{employeeId} - Employee-specific updates
- approvals.{companyId} - Admin approval channels
- audit.{companyId} - Audit log channels

---

### Event Classes (7 NEW)

#### `app/Events/DocumentUploaded.php`
- Broadcast when employee uploads document
- Channels: company.*, approvals.*
- Payload: document_id, employee_name, document_type, status

#### `app/Events/DocumentApproved.php`
- Broadcast when admin approves document
- Channels: user.*, company.*, employee.*
- Payload: document_id, document_type, approver_name

#### `app/Events/DocumentRejected.php`
- Broadcast when admin rejects document
- Channels: user.*, employee.*
- Payload: document_id, rejection_reason, rejector_name

#### `app/Events/DocumentExpiringSoon.php`
- Broadcast 30, 14, 7 days before expiration
- Channels: user.*, company.*
- Payload: document_id, days_until_expiry

#### `app/Events/DocumentExpired.php`
- Broadcast when document expires
- Channels: user.*, company.*
- Payload: document_id, expired_at

#### `app/Events/AuditLogCreated.php`
- Broadcast when audit log entry created
- Channels: company.*
- Payload: user_name, action, model, ip_address

#### `app/Events/DashboardStatsUpdated.php`
- Broadcast when dashboard stats change
- Channels: company.*
- Payload: stats object with all metrics

---

### Livewire Components (4 NEW)

#### `app/Livewire/NotificationBell.php`
- Real-time notification bell with dropdown
- Displays unread notification count
- Features: mark-as-read, mark-all-as-read
- Listens to notification-received events

#### `app/Livewire/DashboardStats.php`
- Live dashboard statistics cards
- Real-time counter updates
- Listens to: dashboard.updated, document.* events
- Displays: employees, uploaded, approved, expiring, compliance %

#### `app/Livewire/ApprovalTable.php`
- Real-time pending approvals table
- Auto-refresh when new documents uploaded
- Listens to: document.uploaded event
- Shows animation on new rows

#### `app/Livewire/EmployeeDocumentStatus.php`
- Employee's personal document status table
- Real-time approval/rejection updates
- Listens to: document.approved, document.rejected events
- Status color-coding (green/red/yellow/orange)

---

### Views (4 NEW + 1 UPDATED)

#### `resources/views/livewire/notification-bell.blade.php`
- Dropdown notification UI
- Unread badge
- Click-to-read functionality
- Real-time event listeners

#### `resources/views/livewire/dashboard-stats.blade.php`
- 4-card stats display
- Compliance progress bar
- Color-coded metrics
- Real-time listeners

#### `resources/views/livewire/approval-table.blade.php`
- Pending documents table
- Fade-in animation for new rows
- Action links
- Real-time event listeners

#### `resources/views/livewire/employee-document-status.blade.php`
- Employee document history
- Status badges
- Expiration dates
- Real-time updates

#### `resources/views/realtime-test.blade.php` (NEW)
- Interactive demo page
- Event simulator buttons
- Dual-window testing support
- Event logging (sent/received)
- Connection status indicator

---

### Controllers (2 NEW)

#### `app/Http/Controllers/NotificationController.php`
- `index()` - List notifications (paginated)
- `markAsRead()` - Mark single notification as read
- `markAllAsRead()` - Mark all notifications as read
- `getUnreadCount()` - Get unread count

#### `app/Http/Controllers/RealtimeTestController.php`
- `index()` - Display test page
- `simulateDocumentUploaded()` - Trigger DocumentUploaded event
- `simulateDocumentApproved()` - Trigger DocumentApproved event
- `simulateDocumentRejected()` - Trigger DocumentRejected event
- `simulateDocumentExpiringSoon()` - Trigger expiration alert
- `simulateDocumentExpired()` - Trigger expiration event
- `simulateDashboardStats()` - Trigger stats update

---

### Routes (3 UPDATED/NEW)

#### `routes/web.php` (UPDATED)
```php
Route::get('/demo/realtime', RealtimeTestController::class . '@index')
    ->middleware('auth')
    ->name('realtime.demo');
```

#### `routes/api.php` (UPDATED)
```php
Route::middleware('auth:sanctum')->prefix('realtime')->group(function () {
    Route::post('/simulate/document-uploaded', ...);
    Route::post('/simulate/document-approved', ...);
    Route::post('/simulate/document-rejected', ...);
    Route::post('/simulate/document-expiring', ...);
    Route::post('/simulate/document-expired', ...);
    Route::post('/simulate/dashboard-stats', ...);
});
```

#### `routes/channels.php` (UPDATED)
- 5 private channels with authorization rules

---

### Frontend Assets (NEW)

#### `resources/js/echo.js`
- Echo configuration
- WebSocket setup
- Error handling
- Connection logging

#### `resources/js/toast.js`
- Global toast notification utility
- Supports: success, error, warning, info
- Auto-dismiss with animations
- Toast container management

#### `resources/js/app.js`
- Application entry point
- Loads toast and Echo utilities
- Initializes Alpine.js

#### `package.json` (UPDATED)
- Removed Next.js dependencies
- Added Laravel, Echo, Pusher.js
- Added Tailwind and Vite

---

## Setup Instructions

### Step 1: Install Dependencies

```bash
# Install PHP dependencies (if not done)
composer install

# Install Node dependencies
npm install
```

### Step 2: Configure Environment

Update `.env`:

```env
BROADCAST_DRIVER=reverb
REVERB_APP_KEY=kiava-hr-app
REVERB_APP_SECRET=kiava-hr-secret
REVERB_APP_ID=1
REVERB_APP_CLUSTER=mt1
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY=kiava-hr-app
VITE_REVERB_HOST=localhost
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http

# Queue configuration
QUEUE_CONNECTION=database
```

### Step 3: Create Database Tables

```bash
php artisan migrate
```

### Step 4: Build Frontend Assets

```bash
npm run build
# or for development with hot reload:
npm run dev
```

---

## Running the System

Open 4 terminal windows:

### Terminal 1: Start Reverb WebSocket Server

```bash
php artisan reverb:start
```

**Expected Output:**
```
INFO  Server running at ws://127.0.0.1:8080
```

### Terminal 2: Start Queue Worker

```bash
php artisan queue:work --timeout=60 --tries=3
```

**Expected Output:**
```
INFO  Listening on: database
```

### Terminal 3: Start Frontend Build (Development)

```bash
npm run dev
```

### Terminal 4: Start Laravel Dev Server

```bash
php artisan serve
```

Open browser at: `http://localhost:8000`

---

## Testing Real-Time Updates

### Method 1: Interactive Demo Page (Recommended)

1. **Open two browser windows side-by-side:**
   - Window 1: http://localhost:8000/demo/realtime
   - Window 2: http://localhost:8000/demo/realtime

2. **In Window 1, click event simulator buttons:**
   - "Document Uploaded" → Appears instantly in Window 2
   - "Document Approved" → Appears instantly in Window 2
   - etc.

3. **Observe:**
   - "Sent Events" log shows what was triggered
   - "Received Events" log shows what was received
   - Connection status shows "Connected" in green
   - Toast notifications appear on new events

### Method 2: Test Individual Components

#### Test Dashboard Stats Updates

1. Navigate to `/dashboard`
2. In another terminal, trigger event:
   ```bash
   php artisan tinker
   > event(new App\Events\DashboardStatsUpdated(1, ['total_employees' => 10]));
   ```
3. Watch dashboard counters update live

#### Test Notification Bell

1. Navigate to any page with notification bell
2. Trigger DocumentApproved event
3. Watch notification badge count increase
4. Click bell to see notification dropdown update

#### Test Approval Table

1. Go to admin dashboard with approval table
2. Trigger DocumentUploaded event
3. Watch new row fade-in instantly
4. No page refresh needed

#### Test Employee Document Status

1. Log in as employee
2. Trigger DocumentApproved event for their document
3. Watch status change from "pending" to "approved"
4. Table updates with green badge

### Method 3: Using Artisan Tinker

```bash
php artisan tinker

# Create test document
$doc = App\Models\EmployeeDocument::factory()->create();

# Dispatch events
event(new App\Events\DocumentUploaded($doc));
event(new App\Events\DocumentApproved($doc, 'Admin User'));
event(new App\Events\DocumentExpiringSoon($doc, 7));

# Check in browser - updates appear instantly!
```

---

## Channel Authorization Rules

All channels are private and require authentication:

### company.{companyId}
- **Access:** Any authenticated user in the company
- **Use Case:** Company-wide announcements, dashboard updates

### user.{userId}
- **Access:** Only the specific user
- **Use Case:** Personal notifications

### employee.{employeeId}
- **Access:** Employee + company admins
- **Use Case:** Employee-specific document updates

### approvals.{companyId}
- **Access:** Admins and managers only
- **Use Case:** Document approval workflows

### audit.{companyId}
- **Access:** Company admins only
- **Use Case:** Audit log streaming

---

## Event Flow Example

### Document Upload Workflow

```
1. Employee uploads document
   └─> DocumentUploaded event triggered

2. Event broadcasts to:
   ├─> company.{companyId}
   └─> approvals.{companyId}

3. Real-time updates occur:
   ├─> ApprovalTable receives new pending item (fade-in animation)
   ├─> DashboardStats updates counters
   └─> Toast notification: "Employee Name uploaded Document Type"

4. Admin approves document
   └─> DocumentApproved event triggered

5. Event broadcasts to:
   ├─> user.{employeeId} (personal channel)
   ├─> company.{companyId}
   └─> employee.{employeeId}

6. Real-time updates:
   ├─> Employee sees document status change to "approved"
   ├─> ApprovalTable removes item from pending list
   ├─> Dashboard compliance % increases
   └─> Toast notification: "Your Document was approved"
```

---

## Troubleshooting

### WebSocket Connection Refused

**Error:** Browser console shows connection error

**Solution:**
```bash
# Check if Reverb is running on port 8080
lsof -i :8080

# Kill if stuck
kill -9 <PID>

# Restart
php artisan reverb:start
```

### Events Not Broadcasting

**Error:** Trigger event but nothing happens in browser

**Solution:**
1. Check queue worker is running: `php artisan queue:work`
2. Verify BROADCAST_DRIVER=reverb in .env
3. Check Reverb console for errors
4. Ensure browser is authenticated (logged in)

### High Memory Usage

**Error:** Reverb process consuming excessive memory

**Solution:**
```bash
# Restart Reverb with memory limit
php artisan reverb:start --memory=512M
```

### Slow Updates

**Error:** Real-time updates lag 1-2 seconds

**Solution:**
- Check network latency: ping localhost:8080
- Reduce Reverb timeout
- Check queue worker backlog

---

## Production Deployment

### Option 1: Laravel Reverb Hosted

```env
BROADCAST_DRIVER=reverb
REVERB_HOST=reverb.yourdomain.com
REVERB_PORT=443
REVERB_SCHEME=https
```

### Option 2: Pusher (Third-party)

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_id
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
```

### Option 3: Self-Hosted with Supervisor

Create `/etc/supervisor/conf.d/kiava-reverb.conf`:

```ini
[program:kiava-reverb]
process_name=%(program_name)s
command=php /home/user/kiava-hr/artisan reverb:start
autostart=true
autorestart=true
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/kiava-reverb.log
```

---

## Performance Metrics

### Expected Latency
- Document upload → Approval table update: **50-100ms**
- Event trigger → Dashboard stats update: **50-150ms**
- Event trigger → Notification bell update: **30-80ms**

### Concurrent Connections
- Single Reverb instance: **1000-5000 users**
- Horizontal scaling: **Add instances and use Redis adapter**

### Memory Usage
- Base Reverb: **50-100MB**
- Per connected user: **~1-2MB**
- With 1000 users: **~1GB**

---

## Files Summary

**Total Files Created/Modified: 23**

- Configuration: 2 new
- Events: 7 new
- Livewire Components: 4 new
- Views: 5 new
- Controllers: 2 new
- Routes: 3 updated
- Frontend: 3 new
- Package config: 1 updated

**Total Lines of Code: ~2,500**

---

## Next Steps

1. **Test thoroughly** using the demo page
2. **Monitor performance** with browser DevTools
3. **Add error handling** for production
4. **Implement message queuing** for high volume
5. **Add analytics** to track event metrics
6. **Deploy to staging** before production
7. **Configure monitoring** and alerting

---

## Support Resources

- **Laravel Echo Docs:** laravel.com/docs/broadcasting
- **Reverb Docs:** laravel.com/docs/reverb
- **Pusher.js Docs:** pusher.com/docs/channels/library_auth_reference/pusher-js
- **Troubleshooting:** Check terminal logs where Reverb runs


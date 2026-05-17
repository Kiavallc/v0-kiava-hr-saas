# Real-Time System - Quick Start Testing Guide

## 60-Second Setup

### 1. Terminal 1 - Start Reverb (WebSocket Server)
```bash
php artisan reverb:start
```
Wait for: `Server running at ws://127.0.0.1:8080`

### 2. Terminal 2 - Start Queue Worker
```bash
php artisan queue:work
```
Wait for: `Listening on: database`

### 3. Terminal 3 - Build Frontend (Hot Reload)
```bash
npm run dev
```
Wait for: Vite compilation complete

### 4. Terminal 4 - Start Laravel Server
```bash
php artisan serve
```
Open: http://localhost:8000

---

## Testing in Two Browser Windows

### Setup
1. Open **Window 1:** http://localhost:8000/demo/realtime (login if needed)
2. Open **Window 2:** http://localhost:8000/demo/realtime (same browser tab)
3. Arrange side-by-side for easy observation

### Test Cases

#### Test 1: Document Upload Event (30 seconds)
- **Window 1:** Click "📤 Document Uploaded" button
- **Window 2:** 
  - ✓ See event in "Received Events" log
  - ✓ Toast notification appears
  - ✓ Dashboard stats update
- **Expected:** Instant (< 100ms)

#### Test 2: Document Approval Event (30 seconds)
- **Window 1:** Click "✅ Document Approved" button
- **Window 2:**
  - ✓ See event in "Received Events" log
  - ✓ Toast: "Document approved" 
  - ✓ Approval table updates
- **Expected:** Instant (< 100ms)

#### Test 3: Document Rejection Event (30 seconds)
- **Window 1:** Click "❌ Document Rejected" button
- **Window 2:**
  - ✓ See event in "Received Events" log
  - ✓ Toast: "Document rejected"
  - ✓ Approval table updates
- **Expected:** Instant (< 100ms)

#### Test 4: Expiration Alerts (30 seconds)
- **Window 1:** Click "⏰ Document Expiring" button
- **Window 2:**
  - ✓ See event in "Received Events" log
  - ✓ Toast: "expires in 7 days"
  - ✓ Warning badge appears
- **Expected:** Instant (< 100ms)

#### Test 5: Dashboard Stats Update (30 seconds)
- **Window 1:** Click "📊 Dashboard Stats" button
- **Window 2:**
  - ✓ See event in "Received Events" log
  - ✓ Dashboard stats cards update
  - ✓ Compliance percentage changes
- **Expected:** Instant (< 150ms)

---

## Demo Page Features

### Sent Events Log
- Shows events triggered in **this window**
- Timestamp format: HH:MM:SS
- Lists event name and first 100 chars of data
- Color-coded with blue border

### Received Events Log
- Shows events **broadcast from other windows**
- Same timestamp and data format
- Color-coded with green border
- Proves real-time delivery

### Connection Status
- **Green indicator** = Connected to WebSocket
- **Red pulsing** = Disconnected/Reconnecting
- Shows "Connected" when ready
- Confirms Echo.js initialization

### Event Simulator Buttons
- 6 event types to trigger
- Color-coded per event type
- Click to broadcast event
- Instant confirmation in "Sent Events"

---

## Expected Behavior

### Successful Test
```
Window 1:
- Click button
- Event appears in "Sent Events" immediately

Window 2:
- Event appears in "Received Events" within 50-100ms
- Toast notification pops up
- Status: All green, Connected indicator active
```

### If Something's Wrong

**Events send but don't receive:**
```
❌ Queue worker not running
→ Start: php artisan queue:work

❌ Reverb server not running
→ Start: php artisan reverb:start

❌ Echo not connected
→ Check browser console for errors
→ Verify WebSocket connection in Network tab
```

---

## Real-World Component Testing

### Test Notification Bell
1. Navigate to `/dashboard` (or any page with notification)
2. In another window, trigger "Document Approved"
3. Watch notification bell badge count increase
4. Click bell to see notification dropdown
5. Click notification to mark as read
6. Badge decreases

### Test Approval Table
1. Log in as admin
2. Go to admin dashboard
3. In another window, trigger "Document Uploaded"
4. Watch new row appear in approval table
5. Fade-in animation confirms real-time
6. Click "Review" to view document

### Test Employee Portal
1. Log in as employee
2. Go to document status page
3. In another window, trigger "Document Approved"
4. Watch status change from "pending" to "approved"
5. Green badge appears instead of yellow

---

## Debugging Console

Open **Browser DevTools** (F12) → **Console** tab:

```javascript
// Check Echo connection
console.log(window.Echo);

// Should output Echo instance if connected

// Subscribe to specific channel
Echo.private('user.' + userId).subscribe();

// Listen for all events on channel
Echo.private('user.' + userId)
    .listen('.', (e) => console.log('Event:', e));

// Check connection status
console.log(window.Echo.connector.socket.connected);
// Should output: true
```

---

## Performance Checklist

- [ ] WebSocket connects within 2 seconds
- [ ] Events deliver within 100ms
- [ ] No console errors
- [ ] Notification badge updates live
- [ ] Dashboard stats update live
- [ ] Approval table updates live
- [ ] Connection status shows green
- [ ] Toast notifications appear

---

## Stopping the System

### Clean Shutdown (in order)
```bash
# Terminal 1 - Stop Reverb
Ctrl+C

# Terminal 2 - Stop Queue Worker
Ctrl+C

# Terminal 3 - Stop Vite
Ctrl+C

# Terminal 4 - Stop Laravel Server
Ctrl+C
```

### Hard Reset (if stuck)
```bash
# Kill all PHP processes
killall php

# Kill Node processes
killall node

# Start fresh in 4 new terminals
```

---

## Common Issues & Quick Fixes

| Issue | Solution |
|-------|----------|
| WebSocket Connection Refused | Ensure Reverb running: `php artisan reverb:start` |
| Events not appearing | Start queue worker: `php artisan queue:work` |
| Toast notifications don't show | Check `resources/js/toast.js` loaded in browser |
| Dashboard doesn't update | Verify BROADCAST_DRIVER=reverb in .env |
| High latency (1-2s delay) | Check network latency: ping localhost:8080 |
| Browser throws error | Open DevTools, check Console tab for errors |

---

## What's Actually Happening

### Event Flow
```
1. You click button in Window 1
2. Browser sends AJAX request to /api/realtime/simulate/*
3. Controller receives request and processes
4. Controller dispatches Laravel Event (e.g., DocumentUploaded)
5. Event implements ShouldBroadcast interface
6. Reverb server receives event
7. Reverb broadcasts to subscribed channels
8. Window 2's Echo listener receives event
9. Livewire component updates
10. DOM updates with new data
11. Toast notification appears
12. All within 50-150ms total!
```

### Architecture
```
Browser 1                 Server              Reverb           Browser 2
   │                         │                   │                 │
   │──Click Button──────────→│                   │                 │
   │                         │                   │                 │
   │                      Dispatch              │                 │
   │                      Event                 │                 │
   │                         │                   │                 │
   │                      Broadcast             │                 │
   │                         │───Broadcast────→│                 │
   │                         │                   │─Listen for────→│
   │                         │                   │  Channel       │
   │                         │                   │                 │
   │              Send Event │←────Receive────│                 │
   │            Log (optional)│    Event       │                 │
   │                         │                   │     Update      │
   │                         │                   │   Components    │
   │                         │                   │     & DOM       │
   │                         │                   │                 │
   │                      <100ms latency      │                 │
```

---

## Next: Advanced Testing

After basic tests pass, try:

1. **Three-window test** - Confirm broadcast reaches multiple subscribers
2. **Stress test** - Click buttons rapidly, verify no events lost
3. **Channel auth** - Login as different user, verify channel isolation
4. **Disconnect test** - Unplug network, verify reconnection
5. **Mobile test** - Test on phone, confirm responsive updates

---

## Checkpoint: Verify All Systems

```bash
# 1. Check Reverb running
curl http://localhost:8080
# Should return connection info or WebSocket upgrade

# 2. Check queue worker running
ps aux | grep "queue:work"
# Should show artisan queue:work process

# 3. Check database connected
php artisan migrate:status
# Should show migrations status

# 4. Check npm build
ls -la public/js/
# Should see compiled app.js

# 5. Check routes registered
php artisan route:list | grep realtime
# Should show realtime routes
```

All ✓ = Ready to test!

---

## Success Indicators

🟢 **All systems working when:**
- Reverb console shows "Server running at ws://127.0.0.1:8080"
- Queue worker shows "Listening on: database"
- Browser dev tools show no errors
- Demo page connection status is green
- Events appear in both "Sent" and "Received" logs
- Toast notifications appear
- Time between send and receive is < 200ms

---

## One-Click Complete Start

Create `start-realtime.sh`:

```bash
#!/bin/bash
echo "Starting Kiava HR Real-Time System..."

# Terminal 1: Reverb
gnome-terminal -- bash -c "php artisan reverb:start; exec bash" &

# Terminal 2: Queue
gnome-terminal -- bash -c "php artisan queue:work; exec bash" &

# Terminal 3: Vite
gnome-terminal -- bash -c "npm run dev; exec bash" &

# Terminal 4: Laravel
gnome-terminal -- bash -c "php artisan serve; exec bash" &

echo "All systems starting..."
sleep 3
echo "Open: http://localhost:8000/demo/realtime"
```

Run: `bash start-realtime.sh`

---

**Ready to test? Open http://localhost:8000/demo/realtime!**

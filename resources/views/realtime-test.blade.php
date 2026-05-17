@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Real-Time Events Test Page</h1>
        <p class="text-gray-600">Open this page in two browser windows side-by-side to test real-time updates</p>
    </div>

    <!-- Event Simulator -->
    <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
        <h2 class="text-xl font-semibold mb-6">Event Simulator</h2>
        <p class="text-sm text-gray-600 mb-4">Click buttons below to simulate events. Watch for updates in real-time!</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Document Uploaded -->
            <button onclick="triggerEvent('document-uploaded')" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded transition">
                📤 Document Uploaded
            </button>

            <!-- Document Approved -->
            <button onclick="triggerEvent('document-approved')" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded transition">
                ✅ Document Approved
            </button>

            <!-- Document Rejected -->
            <button onclick="triggerEvent('document-rejected')" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded transition">
                ❌ Document Rejected
            </button>

            <!-- Document Expiring Soon -->
            <button onclick="triggerEvent('document-expiring')" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded transition">
                ⏰ Document Expiring
            </button>

            <!-- Document Expired -->
            <button onclick="triggerEvent('document-expired')" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded transition">
                🚫 Document Expired
            </button>

            <!-- Dashboard Stats -->
            <button onclick="triggerEvent('dashboard-stats')" class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-4 rounded transition">
                📊 Dashboard Stats
            </button>
        </div>
    </div>

    <!-- Event Log -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Outgoing Events -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Sent Events</h2>
                <button onclick="clearLog('sent')" class="text-sm text-gray-600 hover:text-gray-900">Clear</button>
            </div>
            <div id="sent-log" class="bg-gray-50 rounded p-4 h-64 overflow-y-auto font-mono text-sm">
                <div class="text-gray-500">Waiting for events...</div>
            </div>
        </div>

        <!-- Incoming Events -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Received Events</h2>
                <button onclick="clearLog('received')" class="text-sm text-gray-600 hover:text-gray-900">Clear</button>
            </div>
            <div id="received-log" class="bg-gray-50 rounded p-4 h-64 overflow-y-auto font-mono text-sm">
                <div class="text-gray-500">Waiting for events...</div>
            </div>
        </div>
    </div>

    <!-- Connection Status -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <div class="flex items-center gap-4">
            <div id="connection-status" class="w-4 h-4 bg-red-500 rounded-full"></div>
            <div>
                <p class="font-semibold" id="status-text">Disconnected</p>
                <p class="text-sm text-gray-600" id="status-detail">Waiting for WebSocket connection...</p>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>

<script>
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    const companyId = document.querySelector('meta[name="company-id"]')?.content;
    let eventCount = { sent: 0, received: 0 };

    function logEvent(type, event, data) {
        const logId = type === 'sent' ? 'sent-log' : 'received-log';
        const log = document.getElementById(logId);
        const timestamp = new Date().toLocaleTimeString();
        
        eventCount[type === 'sent' ? 'sent' : 'received']++;
        
        const entry = document.createElement('div');
        entry.className = 'mb-2 p-2 bg-white rounded border-l-4 ' + (type === 'sent' ? 'border-blue-500' : 'border-green-500');
        entry.innerHTML = `
            <strong>${timestamp}</strong><br/>
            <span class="text-gray-700">${event}</span><br/>
            <span class="text-gray-600 text-xs">${JSON.stringify(data).substring(0, 100)}...</span>
        `;
        
        if (log.children.length > 0 && log.children[0].textContent.includes('Waiting')) {
            log.innerHTML = '';
        }
        log.insertBefore(entry, log.firstChild);
    }

    function clearLog(type) {
        const logId = type === 'sent' ? 'sent-log' : 'received-log';
        document.getElementById(logId).innerHTML = '<div class="text-gray-500">Log cleared</div>';
    }

    function updateConnectionStatus(connected) {
        const status = document.getElementById('connection-status');
        const text = document.getElementById('status-text');
        const detail = document.getElementById('status-detail');
        
        if (connected) {
            status.className = 'w-4 h-4 bg-green-500 rounded-full';
            text.textContent = 'Connected';
            detail.textContent = 'WebSocket is active and events are flowing';
        } else {
            status.className = 'w-4 h-4 bg-red-500 rounded-full animate-pulse';
            text.textContent = 'Disconnected';
            detail.textContent = 'Waiting for WebSocket connection...';
        }
    }

    function triggerEvent(eventType) {
        let endpoint = '';
        let eventName = '';
        
        switch(eventType) {
            case 'document-uploaded':
                endpoint = '/api/realtime/simulate/document-uploaded';
                eventName = 'DocumentUploaded';
                break;
            case 'document-approved':
                endpoint = '/api/realtime/simulate/document-approved';
                eventName = 'DocumentApproved';
                break;
            case 'document-rejected':
                endpoint = '/api/realtime/simulate/document-rejected';
                eventName = 'DocumentRejected';
                break;
            case 'document-expiring':
                endpoint = '/api/realtime/simulate/document-expiring';
                eventName = 'DocumentExpiringSoon';
                break;
            case 'document-expired':
                endpoint = '/api/realtime/simulate/document-expired';
                eventName = 'DocumentExpired';
                break;
            case 'dashboard-stats':
                endpoint = '/api/realtime/simulate/dashboard-stats';
                eventName = 'DashboardStatsUpdated';
                break;
        }

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + document.querySelector('meta[name="api-token"]')?.content,
                'Content-Type': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            logEvent('sent', eventName, data.data || {});
        })
        .catch(err => {
            console.error('Error:', err);
            logEvent('sent', eventName + ' (ERROR)', { error: err.message });
        });
    }

    // Setup Echo listeners
    if (window.Echo) {
        updateConnectionStatus(true);

        // Company channel
        Echo.private('company.' + companyId)
            .listen('DocumentUploaded', (e) => {
                logEvent('received', 'DocumentUploaded', e);
            })
            .listen('DashboardUpdated', (e) => {
                logEvent('received', 'DashboardUpdated', e);
            });

        // User channel
        Echo.private('user.' + userId)
            .listen('DocumentApproved', (e) => {
                logEvent('received', 'DocumentApproved', e);
            })
            .listen('DocumentRejected', (e) => {
                logEvent('received', 'DocumentRejected', e);
            })
            .listen('DocumentExpiringSoon', (e) => {
                logEvent('received', 'DocumentExpiringSoon', e);
            })
            .listen('DocumentExpired', (e) => {
                logEvent('received', 'DocumentExpired', e);
            });

        // Error handling
        Echo.connector.socket.on('error', (error) => {
            console.error('WebSocket Error:', error);
            updateConnectionStatus(false);
        });

        Echo.connector.socket.on('reconnect', () => {
            updateConnectionStatus(true);
        });
    } else {
        updateConnectionStatus(false);
        console.error('Echo not initialized - check WebSocket connection');
    }
</script>
@endsection

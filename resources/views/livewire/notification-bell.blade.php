<div class="notification-bell">
    <button @click="$dispatch('toggle-notifications')" class="relative p-2 text-gray-600 hover:text-gray-900">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    <div class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 hidden" id="notifications-dropdown">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Notifications</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-sm text-blue-600 hover:text-blue-800">Mark all as read</button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($recentNotifications as $notification)
                <div class="px-4 py-3 border-b hover:bg-gray-50 cursor-pointer {{ !$notification['read'] ? 'bg-blue-50' : '' }}"
                     wire:click="markAsRead({{ $notification['id'] }})">
                    <p class="text-sm font-medium text-gray-900">{{ $notification['title'] }}</p>
                    <p class="text-sm text-gray-600">{{ $notification['message'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $notification['created_at'] }}</p>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">
                    <p>No notifications</p>
                </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-gray-200 text-center">
            <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View all notifications</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bell = document.querySelector('.notification-bell');
        const dropdown = document.getElementById('notifications-dropdown');
        
        if (bell && dropdown) {
            bell.querySelector('button').addEventListener('click', function() {
                dropdown.classList.toggle('hidden');
            });
            
            document.addEventListener('click', function(e) {
                if (!bell.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }
    });

    // Listen for real-time events
    if (window.Echo) {
        Echo.private('user.' + userId)
            .listen('DocumentApproved', (e) => {
                Livewire.dispatch('notification-received', e);
                window.showToast('success', e.message);
            })
            .listen('DocumentRejected', (e) => {
                Livewire.dispatch('notification-received', e);
                window.showToast('error', e.message);
            })
            .listen('DocumentExpiringSoon', (e) => {
                Livewire.dispatch('notification-received', e);
                window.showToast('warning', e.message);
            });
    }
</script>

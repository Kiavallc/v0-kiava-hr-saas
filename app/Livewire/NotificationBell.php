<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;
    public array $recentNotifications = [];

    public function mount(): void
    {
        $this->updateUnreadCount();
        $this->loadRecentNotifications();
    }

    #[On('notification-received')]
    public function onNotificationReceived(array $data): void
    {
        $this->updateUnreadCount();
        $this->loadRecentNotifications();
    }

    public function updateUnreadCount(): void
    {
        $this->unreadCount = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }

    public function loadRecentNotifications(): void
    {
        $this->recentNotifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'type' => $n->type,
                'read' => $n->read_at !== null,
                'created_at' => $n->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    public function markAsRead(int $notificationId): void
    {
        Notification::find($notificationId)?->update(['read_at' => now()]);
        $this->updateUnreadCount();
        $this->loadRecentNotifications();
    }

    public function markAllAsRead(): void
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        $this->updateUnreadCount();
        $this->loadRecentNotifications();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}

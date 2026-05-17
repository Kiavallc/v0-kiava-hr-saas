<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotificationsCleanup extends Command
{
    protected $signature = 'notifications:cleanup';
    protected $description = 'Clean up old notifications (older than 30 days)';

    public function handle()
    {
        $cutoffDate = Carbon::now()->subDays(30);
        
        $deleted = Notification::where('created_at', '<', $cutoffDate)
            ->where('read', true)
            ->delete();

        $this->info("Cleaned up {$deleted} old notifications");
    }
}

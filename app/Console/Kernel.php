<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check for documents expiring in 30, 14, and 7 days
        $schedule->command('documents:check-expiring')->daily()->at('09:00');

        // Check for expired documents
        $schedule->command('documents:check-expired')->daily()->at('09:15');

        // Clean up old notifications
        $schedule->command('notifications:cleanup')->daily()->at('03:00');

        // Generate daily compliance reports
        $schedule->command('compliance:generate-report')->daily()->at('08:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

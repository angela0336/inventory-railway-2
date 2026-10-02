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
    $schedule->command('members:birthday-vouchers')->dailyAt('08:00');

    $schedule->call(function () {
        \App\Models\MemberVoucher::where('status', 'available')
            ->whereDate('expires_at', '<', now())
            ->update(['status' => 'expired']);
    })->daily();
}

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

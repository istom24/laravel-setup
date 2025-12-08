<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GenerateReport::class,
        \App\Console\Commands\TestTelegramCommand::class,
        \App\Console\Commands\CheckExpiredTasks::class,
        \App\Console\Commands\ViewSchedulerLogs::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('tasks:check-expired')
            ->dailyAt('08:00')
            ->description('Щоденна перевірка прострочених задач');

        $schedule->command('app:generate-report --period=7 --export --silent')
            ->weeklyOn(1, '09:00')
            ->description('Щотижнева генерація звіту');

        $schedule->command('inspire')->hourly();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }

    protected function scheduleTimezone()
    {
        return 'Europe/Kiev';
    }
}

<?php

namespace App\Console\Commands;

use App\Models\SchedulerLog;
use Illuminate\Console\Command;

class ViewSchedulerLogs extends Command
{
    protected $signature = 'scheduler:logs {--limit=10}';
    protected $description = 'Перегляд останніх логів планувальника';

    public function handle()
    {
        $limit = $this->option('limit');
        $logs = SchedulerLog::latest()->limit($limit)->get();

        if ($logs->isEmpty()) {
            $this->info('Логи відсутні');
            return;
        }

        $this->table(
            ['Дата', 'Команда', 'Статус', 'Деталі'],
            $logs->map(function ($log) {
                return [
                    $log->created_at->format('d.m.Y H:i'),
                    $log->command,
                    $log->status,
                    substr($log->output ?? '', 0, 50),
                ];
            })
        );

        return 0;
    }
}

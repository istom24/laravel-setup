<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\SchedulerLog;
use App\Jobs\SendTelegramMessageJob;
use Illuminate\Console\Command;

class CheckExpiredTasks extends Command
{
    protected $signature = 'tasks:check-expired';
    protected $description = 'Перевіряє задачі в статусі in_progress понад 7 днів';

    public function handle()
    {
        SchedulerLog::add('tasks:check-expired', 'started', 'Початок перевірки прострочених задач');

        $cutoffDate = now()->subDays(7);
        $tasks = Task::where('status', 'in_progress')
            ->where('updated_at', '<=', $cutoffDate)
            ->with(['project', 'author', 'assignee'])
            ->get();

        $count = $tasks->count();
        $this->info("Знайдено задач: {$count}");

        foreach ($tasks as $task) {
            $task->update(['status' => 'expired']);
            $this->sendTelegramNotification($task);
        }

        // Додаємо лог про завершення
        SchedulerLog::add('tasks:check-expired', 'completed', "Оброблено {$count} задач");

        $this->info("Оброблено {$count} задач");
        return 0;
    }

    private function sendTelegramNotification(Task $task)
    {
        $chatId = config('services.telegram.chat_id');

        if (!$chatId) {
            return;
        }

        $assigneeName = $task->assignee ? $task->assignee->name : 'Не призначено';

        $message = "Задача прострочена\n\n"
            . "Завдання: {$task->title}\n"
            . "Проєкт: {$task->project->name}\n"
            . "Старий статус: in_progress\n"
            . "Новий статус: expired\n"
            . "Виконавець: {$assigneeName}\n\n"
            . "ID: {$task->id}";

        SendTelegramMessageJob::dispatch($chatId, $message)
            ->onQueue('teleavel');
    }
}

<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Jobs\SendTelegramMessageJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTaskCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'telegram';
    public $delay = 5;

    public function handle(TaskCreated $event)
    {
        $task = $event->task;
        $project = $task->project;
        $author = $task->author;
        $assignee = $task->assignee;

        $assigneeName = $assignee ? $assignee->name : 'Не призначено';

        $message = "<b>Нове завдання</b>\n\n"
            . "<b>Проєкт:</b> {$project->name}\n"
            . "<b>Завдання:</b> {$task->title}\n"
            . "<b>Автор:</b> {$author->name}\n"
            . "<b>Виконавець:</b> {$assigneeName}\n"
            . "<b>Статус:</b> {$task->status}\n"
            . "<b>Пріоритет:</b> {$task->priority}\n"
            . "<b>Термін:</b> " . ($task->due_date ? $task->due_date->format('d.m.Y') : 'Не вказано') . "\n\n"
            . "<i>ID завдання: {$task->id}</i>";

        $chatId = config('services.telegram.chat_id');

        if ($chatId) {
            SendTelegramMessageJob::dispatch(
                $chatId,
                $message,
                [],
                'task_created'
            )->onQueue('telegram');
        }
    }
}

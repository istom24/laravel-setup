<?php

namespace App\Jobs;

use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTelegramMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chatId;
    public $message;
    public $options;
    public $eventType;

    public function __construct($chatId, $message, $options = [], $eventType = null)
    {
        $this->chatId = $chatId;
        $this->message = $message;
        $this->options = $options;
        $this->eventType = $eventType;
    }

    public function handle(TelegramService $telegramService)
    {
        Log::info('Відправка повідомлення в Telegram через Job', [
            'chat_id' => $this->chatId,
            'event_type' => $this->eventType,
            'message_length' => strlen($this->message),
        ]);

        $result = $telegramService->sendMessage($this->chatId, $this->message, $this->options);

        if (!$result['success']) {
            Log::error('Помилка при відправці повідомлення в Telegram через Job', [
                'error' => $result['error'],
                'chat_id' => $this->chatId,
            ]);
            $this->fail(new \Exception($result['error']));
        }
    }
}

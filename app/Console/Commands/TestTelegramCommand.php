<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TestTelegramCommand extends Command
{
    protected $signature = 'telegram:test
                            {message? : Текст повідомлення}
                            {--chat-id= : ID чату}';

    protected $description = 'Тестування надсилання повідомлень у Telegram';

    public function handle(TelegramService $telegramService)
    {
        $this->info('Тестування інтеграції з Telegram...');

        // Перевірка конфігурації
        $token = config('services.telegram.token');
        $chatId = $this->option('chat-id') ?? config('services.telegram.chat_id');

        if (!$token) {
            $this->error('Токен бота не налаштований. Вкажіть TELEGRAM_BOT_TOKEN у .env');
            return 1;
        }

        if (!$chatId) {
            $this->error('ID чату не налаштований. Вкажіть TELEGRAM_CHAT_ID у .env або використайте --chat-id');
            return 1;
        }

        $this->info("Токен бота: " . substr($token, 0, 10) . '...');
        $this->info("ID чату: $chatId");

        // Перевірка доступності бота
        $this->info("\nПеревірка доступності бота...");
        $botInfo = $telegramService->getBotInfo();

        if ($botInfo['success']) {
            $bot = $botInfo['bot'];
            $this->info("Бот доступний: @{$bot['username']} ({$bot['first_name']})");
        } else {
            $this->warn("Не вдалося отримати інформацію про бота: " . ($botInfo['error'] ?? 'Unknown error'));
        }

        $message = $this->argument('message') ?? 'Тестове повідомлення з Laravel застосунку';

        $this->info("\nНадсилання тестового повідомлення...");
        $this->info("Повідомлення: $message");

        $result = $telegramService->sendMessage($chatId, $message);

        if ($result['success']) {
            $this->info("Повідомлення успішно надіслано!");
            $this->info("ID повідомлення: " . ($result['message_id'] ?? 'невідомо'));
        } else {
            $this->error("Помилка при надсиланні: " . ($result['error'] ?? 'Unknown error'));
            return 1;
        }

        return 0;
    }
}

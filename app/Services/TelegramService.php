<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $token;
    protected $baseUrl;

    public function __construct()
    {
        $this->token = config('services.telegram.token');
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }
    public function sendMessage(string $chatId, string $message, array $options = []): array
    {
        try {
            $payload = array_merge([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ], $options);

            Log::info('Відправлення повідомлення в Telegram', [
                'chat_id' => $chatId,
                'message_length' => strlen($message),
                'payload' => $payload,
            ]);

            $response = Http::timeout(10)
                ->retry(3, 1000)
                ->post("{$this->baseUrl}/sendMessage", $payload);

            $result = $response->json();

            if ($response->successful() && $result['ok']) {
                Log::info('Повідомлення успішно надіслано в Telegram', [
                    'message_id' => $result['result']['message_id'] ?? null,
                    'chat_id' => $chatId,
                ]);

                return [
                    'success' => true,
                    'message_id' => $result['result']['message_id'] ?? null,
                    'response' => $result,
                ];
            } else {
                Log::error('Помилка під час надсилання повідомлення в Telegram', [
                    'response' => $result,
                    'status' => $response->status(),
                ]);

                return [
                    'success' => false,
                    'error' => $result['description'] ?? 'Unknown error',
                    'response' => $result,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Виняток при відправленні повідомлення в Telegram', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    public function sendMessageWithRetry(string $chatId, string $message, int $maxRetries = 3): array
    {
        $attempt = 0;

        while ($attempt < $maxRetries) {
            $result = $this->sendMessage($chatId, $message);

            if ($result['success']) {
                return $result;
            }

            $attempt++;
            sleep(2 * $attempt); // Exponential backoff
        }

        return [
            'success' => false,
            'error' => 'Max retries exceeded',
        ];
    }

    public function getBotInfo(): array
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/getMe");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'bot' => $data['result'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => 'Не вдалося отримати інформацію про бота',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}

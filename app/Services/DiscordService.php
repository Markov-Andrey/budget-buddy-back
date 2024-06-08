<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class DiscordService
{
    private Client $client;
    private string $apiUrl;
    private string $channelId;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => rtrim(env('DISCORD_API_URL'), '/') . '/',
            'headers' => [
                'Authorization' => 'Bot ' . env('DISCORD_API_BOT_TOKEN'),
                'Content-Type' => 'application/json',
            ],
        ]);

        $this->apiUrl = env('DISCORD_API_URL');
        $this->channelId = env('DISCORD_CHAT_ID');
    }

    /**
     * Получает последние сообщения из канала.
     *
     * @param int $limit Количество сообщений для получения (по умолчанию 25)
     * @return array Массив сообщений
     * @throws GuzzleException
     */
    public function getMessages(int $limit = 25): array
    {
        $response = $this->client->get($this->apiUrl . 'channels/' . $this->channelId . '/messages', [
            'query' => [
                'limit' => $limit,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Отправляет сообщение в канал Discord.
     *
     * @param string $message Текст сообщения
     */
    public function sendMessage(string $message): void
    {
        try {
            $this->client->post('channels/' . $this->channelId . '/messages', [
                'json' => [
                    'content' => $message,
                ],
            ]);
        } catch (GuzzleException $e) {
            Log::channel('discord')->error('Error sending message to Discord: ' . $e->getMessage());
        }
    }

    /**
     * Добавляет реакцию к сообщению.
     *
     * @param string $messageId Идентификатор сообщения
     * @param string $emoji Эмодзи для добавления в качестве реакции
     * @return bool Возвращает true, если реакция успешно добавлена
     * @throws GuzzleException
     */
    public function addReaction(string $messageId, string $emoji): bool
    {
        $response = $this->client->put($this->apiUrl . 'channels/' . $this->channelId . '/messages/' . $messageId . '/reactions/' . urlencode($emoji) . '/@me');

        return $response->getStatusCode() === 204;
    }

    /**
     * Проверяет, есть ли у сообщения указанная реакция.
     *
     * @param array $message Массив данных сообщения
     * @param string $emoji Эмодзи для проверки
     * @return bool Возвращает true, если реакция присутствует
     */
    public static function hasReaction(array $message, string $emoji): bool
    {
        if (isset($message['reactions'])) {
            foreach ($message['reactions'] as $reaction) {
                if ($reaction['me'] === true && $reaction['emoji']['name'] === $emoji) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Проверяет, содержит ли сообщение определенную строку.
     *
     * @param array $message Сообщение.
     * @param string $name Строка для поиска.
     * @return bool Возвращает true, если сообщение содержит указанную строку, в противном случае возвращает false.
     */
    public static function hasString(array $message, string $name): bool
    {
        return (isset($message['content']) && str_contains($message['content'], $name));
    }

    /**
     * Проверяет, имеет ли сообщение вложения.
     *
     * @param array $message Сообщение.
     * @return bool Возвращает true, если сообщение имеет вложения, в противном случае возвращает false.
     */
    public static function hasAttachments(array $message): bool
    {
        return !empty($message['attachments']);
    }
}

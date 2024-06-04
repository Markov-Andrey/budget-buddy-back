<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class DiscordService
{
    private Client $client;
    private string $apiUrl;
    private string $channelId;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'Authorization' => 'Bot ' . env('DISCORD_API_BOT_TOKEN'),
                'Content-Type' => 'application/json',
            ],
        ]);

        $this->apiUrl = 'https://discord.com/api/v10/';
        $this->channelId = '1245678879151095861';
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

<?php

namespace App\Http\Controllers;
use App\Models\DiscordChat;
use GuzzleHttp\Client;

class DiscordController extends Controller
{
    private object $client;
    private string $emojiView = '👀';
    private string $urlApi;
    private string $channelId;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'Authorization' => 'Bot ' . env('DISCORD_API_BOT_TOKEN'),
            ],
        ]);

        $this->urlApi = 'https://discord.com/api/v10/';
        $this->channelId = '1245678879151095861';
    }

    public function index(): array
    {
        return $this->getMessagesWithNoReaction();
    }

    private function getMessagesWithNoReaction(): array
    {
        $messageId = DiscordChat::query()->where('chat_id', $this->channelId)->value('last_message_id');
        $after = $messageId ? '?after=' . $messageId : '';
        $response = $this->client->get($this->urlApi . 'channels/' . $this->channelId . '/messages' . $after);
        $messages = json_decode($response->getBody(), true);
        $messagesWithNoReaction = [];

        $lastId = null;
        if (!empty($messages)) {
            foreach ($messages as $message) {
                $hasThumbsUpReaction = $this->hasThumbsUpReaction($message);

                if (!$hasThumbsUpReaction) {
                    $this->addThumbsUpReaction($message);
                    $messagesWithNoReaction[] = $message;
                    $lastId = $message['id'];
                }
            }
        }
        // хранение чата и последнего посмотренного сообщения
        $chat = DiscordChat::where('chat_id', $this->channelId)->first();
        if ($lastId) {
            if ($chat) {
                $chat->update(['last_message_id' => $lastId]);
            } else {
                DiscordChat::create([
                    'chat_id' => $this->channelId,
                    'last_message_id' => $lastId
                ]);
            }
        }

        return $messagesWithNoReaction;
    }

    private function hasThumbsUpReaction($message): bool
    {
        if (isset($message['reactions'])) {
            foreach ($message['reactions'] as $reaction) {
                if ($reaction['me'] === true && $reaction['emoji']['name'] === $this->emojiView) {
                    return true;
                }
            }
        }

        return false;
    }

    private function addThumbsUpReaction($message): void
    {
        $messageId = $message['id'];
        $putResponse = $this->client->put($this->urlApi . 'channels/' . $this->channelId . '/messages/' . $messageId . '/reactions/' . $this->emojiView . '/@me');

        $putResponse->getStatusCode() == 204;
    }
}

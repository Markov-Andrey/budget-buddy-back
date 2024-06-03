<?php

namespace App\Http\Controllers;

use App\Models\Receipts;
use App\Models\ReceiptsData;
use App\Models\User;
use App\Services\DiscordService;
use GuzzleHttp\Exception\GuzzleException;

class DiscordController extends Controller
{
    private DiscordService $discord;

    public function __construct()
    {
        $this->discord = new DiscordService;
    }

    /**
     * @throws GuzzleException
     */
    public function index(): array
    {
        $messages = $this->discord->getMessages();
        $messagesNoAttachment = [];
        $messagesWithAttachment = [];

        foreach ($messages as $message) {
            if (!$this->discord->hasReaction($message, '👀') &&
                $this->discord->hasString($message, 'Афи') &&
                $this->discord->hasString($message, 'чек')) {
                if ($this->discord->hasAttachments($message)) {
                    $messagesNoAttachment[] = $message;
                } else {
                    $messagesWithAttachment[] = $message;
                }
            }
        }

        $result = [];

        if (!empty($messagesNoAttachment)) {
            $result['messages'] = $this->processMessagesNoAttachment($messagesNoAttachment);
        }

        if (!empty($messagesWithAttachment)) {
            $result['messages'] = $this->processMessagesWithAttachment($messagesWithAttachment);
        }

        return $result;
    }

    private function processMessagesNoAttachment(array $messages): array
    {
        $processedMessages = [];

        foreach ($messages as $message) {
            $lines = explode("\n", $message['content']);
            array_shift($lines);
            foreach ($lines as $line) {
                $line = trim($line);
                $parts = explode(',', $line);
                $parts = array_map('trim', $parts);

                $count = 1; // По умолчанию количество равно 1
                $weight = 0; // По умолчанию вес равен 0
                $price = 0; // По умолчанию цена равна 0

                foreach ($parts as $part) {
                    if (str_contains($part, 'шт')) {
                        $count = (int) $part;
                    } elseif (str_contains($part, 'л') || str_contains($part, 'кг')) {
                        $weight = (float) $part;
                    } elseif (str_contains($part, 'руб')) {
                        $price = (float) $part;
                    }
                }

                $user = User::query()->where('discord_name', $message['author']['username'])->first();
                $user_id = $user?->id;

                $processedMessages[] = [
                    'user_id' => $user_id, // ID
                    'name' => $parts[0], // Название
                    'quantity' => $count,   // Количество
                    'weight' => $weight, // Вес
                    'price' => $price,    // Цена
                    'datetime' => date('Y-m-d H:i:s', strtotime($message['timestamp'])), // Дата
                ];
            }
        }

        $receipt = new Receipts();
        $receipt->user_id = $processedMessages[0]['user_id'];
        $receipt->datetime = $processedMessages[0]['datetime'];
        $receipt->processed = 1;
        $receipt->save();

        $receiptData = [];
        $sumAmount = 0;

        foreach ($processedMessages as $data){
            if($data['name'] && $data['price']) {

            }
            $receiptData[] = [
                'receipts_id' => $receipt->id,
                'name' => $data['name'],
                'quantity' => $data['quantity'],
                'weight' => $data['weight'],
                'price' => $data['price'],
            ];
            $sumAmount += $data['price'] * $data['quantity'] * 100;
        }
        ReceiptsData::insert($receiptData);
        Receipts::where('id', $receipt->id)->update(['amount' => intval($sumAmount)]);

        return $processedMessages;
    }

    private function processMessagesWithAttachment(array $messages): array
    {
        // Логика обработки сообщений с вложениями
        return $messages;
    }
}

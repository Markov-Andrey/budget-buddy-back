<?php

namespace App\Http\Controllers;

use App\Models\Receipts;
use App\Models\ReceiptsData;
use App\Models\User;
use App\Services\DiscordService;
use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
    public function index(): void
    {
        $messages = $this->discord->getMessages();
        $messagesNoAttachment = [];
        $messagesWithAttachment = [];

        foreach ($messages as $message) {
            if (!$this->discord->hasReaction($message, '👀') &&
                $this->discord->hasString($message, 'Афи') &&
                $this->discord->hasString($message, 'чек')) {
                if ($this->discord->hasAttachments($message)) {
                    $messagesWithAttachment[] = $message;
                } else {
                    $messagesNoAttachment[] = $message;
                }
            }
        }

        if (!empty($messagesNoAttachment)) {
            $this->processMessagesNoAttachment($messagesNoAttachment);
        }

        if (!empty($messagesWithAttachment)) {
            $this->processMessagesWithAttachment($messagesWithAttachment);
        }
    }

    private function processMessagesNoAttachment(array $messages): void
    {
        foreach ($messages as $message) {
            $this->discord->addReaction($message['id'], '👀');

            $processedMessages = [];
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
                    'quantity' => $count, // Количество
                    'weight' => $weight, // Вес
                    'price' => $price, // Цена
                    'datetime' => date('Y-m-d H:i:s', strtotime($message['timestamp'])), // Дата
                ];
            }

            try {
                DB::beginTransaction();

                $receipt = new Receipts();
                $receipt->user_id = $processedMessages[0]['user_id'];
                $receipt->datetime = $processedMessages[0]['datetime'];
                $receipt->processed = 1;
                $receipt->save();

                $receiptData = [];
                $sumAmount = 0;

                foreach ($processedMessages as $data) {
                    if ($data['name'] && $data['price']) {
                        $receiptData[] = [
                            'receipts_id' => $receipt->id,
                            'name' => $data['name'],
                            'quantity' => $data['quantity'],
                            'weight' => $data['weight'],
                            'price' => $data['price'],
                        ];
                        $sumAmount += $data['price'] * $data['quantity'] * 100;
                    }
                }

                ReceiptsData::insert($receiptData);
                Receipts::where('id', $receipt->id)->update(['amount' => intval($sumAmount)]);

                DB::commit();

                $this->discord->addReaction($message['id'], '👍');
            } catch (Exception $e) {
                DB::rollBack();
                Log::error("Error processing message: " . $e->getMessage());
                $this->discord->addReaction($message['id'], '👎');
            }
        }
    }

    /**
     * @throws \Exception
     * @throws GuzzleException
     */
    private function processMessagesWithAttachment(array $messages): void
    {
        foreach ($messages as $message) {
            $user = User::query()->where('discord_name', $message['author']['username'])->first();
            $user_id = $user?->id;

            $this->discord->addReaction($message['id'], '👀');

            try {
                foreach ($message['attachments'] as $newPhoto) {
                    $imageContents = file_get_contents($newPhoto['proxy_url']);
                    if ($imageContents === false) {
                        throw new Exception("Unable to fetch image contents");
                    }

                    $urlPath = parse_url($newPhoto['proxy_url'], PHP_URL_PATH);
                    $imageName = basename($urlPath);
                    $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
                    $imagePath = 'receipts/' . pathinfo($imageName, PATHINFO_FILENAME) . '.' . $imageExtension;
                    Storage::disk('public')->put($imagePath, $imageContents);

                    if (!Storage::disk('public')->exists($imagePath)) {
                        throw new Exception("Failed to save image: $imagePath");
                    }

                    $datetime = new DateTime($message['timestamp']);
                    $formattedDatetime = $datetime->format('Y-m-d H:i:s');

                    $receipt = new Receipts();
                    $receipt->user_id = $user_id;
                    $receipt->datetime = $formattedDatetime;
                    $receipt->image_path = $imagePath;
                    $receipt->save();
                }

                $this->discord->addReaction($message['id'], '👍');
            } catch (Exception $e) {
                Log::error("Error processing message attachment: " . $e->getMessage());
                $this->discord->addReaction($message['id'], '👎');
            }
        }
    }
}

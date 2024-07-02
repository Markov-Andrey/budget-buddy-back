<?php

namespace App\Http\Controllers;

use App\Models\DiscordMessage;
use App\Models\Income;
use App\Models\Receipts;
use App\Models\ReceiptsData;
use App\Models\Subcategory;
use App\Models\User;
use App\Services\DiscordService;
use App\Services\PregMatchService;
use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
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
     * Обработчик сообщений из Discord.
     *
     * @return void
     * @throws GuzzleException
     */
    public function index(): void
    {
        $messages = $this->discord->getMessages();
        $messages = array_reverse($messages); // обработка от старого к новому
        $messagesNoAttachment = [];
        $messagesWithAttachment = [];
        $messagesIncome = [];
        $messagesInvestment = [];

        if (!empty($messages)) {
            foreach ($messages as $message) {
                usleep(300000);
                if (!$this->discord->hasReaction($message, '👀') && $this->discord->hasString($message, 'Афи')) {
                    if ($this->discord->hasString($message, 'чек')) {
                        if ($this->discord->hasAttachments($message)) {
                            $messagesWithAttachment[] = $message;
                        } else {
                            $messagesNoAttachment[] = $message;
                        }
                    }
                    if ($this->discord->hasString($message, 'доход')) {
                        $messagesIncome[] = $message;
                    }
                    if ($this->discord->hasString($message, 'инвестиции')) {
                        $messagesInvestment[] = $message;
                    }
                }
            }
        }

        if (!empty($messagesNoAttachment)) {
            $this->processMessagesNoAttachment($messagesNoAttachment);
        }

        if (!empty($messagesWithAttachment)) {
            $this->processMessagesWithAttachment($messagesWithAttachment);
        }

        if (!empty($messagesIncome)) {
            $this->processMessagesIncome($messagesIncome);
        }

        if (!empty($messagesInvestment)) {
            $this->processMessagesInvestment($messagesInvestment);
        }

        $count = count($messagesNoAttachment) + count($messagesWithAttachment) + count($messagesIncome);
        if ($count > 0) {
            $message = DiscordMessage::getRandomMessageByCode('accept');
            if ($message) $this->discord->sendMessage($message);
            Log::channel('discord')->info("Received {$count} items from Discord");
        } else {
            $message = DiscordMessage::getRandomMessageByCode('no_requests');
            if ($message) $this->discord->sendMessage($message);
            Log::channel('discord')->info('Received empty array from Discord');
        }
    }

    /**
     * Обрабатывает сообщения-чеки с текстовыми данными.
     *
     * @param array $messages Массив сообщений без вложенияй.
     * @return void
     * @throws GuzzleException
     */
    private function processMessagesNoAttachment(array $messages): void
    {
        foreach ($messages as $message) {
            usleep(300000);
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
                    $count = PregMatchService::findKeyReturnFloat($part, config('units.quantity')) ?? $count;
                    $weight = PregMatchService::findKeyReturnFloat($part, config('units.weight')) ?? $weight;
                    $price = PregMatchService::findKeyReturnFloat($part, config('units.price')) ?? $price;
                }

                Log::channel('discord')->info($parts[0]. '-' .$count . '-' . $weight . '-' . $price);

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

                Log::channel('discord')->info(json_encode($processedMessages));
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
                    Log::channel('discord')->info($data['name'].' '.$data['price']);
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
                Log::channel('discord')->info(json_encode($receiptData));

                ReceiptsData::insert($receiptData);
                Receipts::where('id', $receipt->id)->update(['amount' => intval($sumAmount)]);

                DB::commit();

                $this->discord->addReaction($message['id'], '👍');
                Log::channel('discord')->info('MessagesNoAttachment processed successfully: ' . $message['id']);
            } catch (Exception $e) {
                DB::rollBack();
                $this->discord->addReaction($message['id'], '👎');
                Log::channel('discord')->error("Error processing MessagesNoAttachment: " . $e->getMessage());
            }
        }
    }

    /**
     * Обрабатывает сообщения-чеки с вложениями.
     *
     * @param array $messages Массив сообщений с вложениями.
     * @return void
     * @throws GuzzleException
     */
    private function processMessagesWithAttachment(array $messages): void
    {
        foreach ($messages as $message) {
            usleep(300000);
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
                Log::channel('discord')->info('MessagesWithAttachment processed successfully: ' . $message['id']);
            } catch (Exception $e) {
                Log::channel('discord')->error("Error processing message attachment: " . $e->getMessage());
                $this->discord->addReaction($message['id'], '👎');
            }
        }
    }

    /**
     * Обрабатывает сообщения о доходе и сохраняет их в базе данных.
     *
     * @param array $messages Массив сообщений о доходе.
     * @return void
     * @throws GuzzleException
     */
    private function processMessagesIncome(array $messages): void
    {
        foreach ($messages as $message) {
            usleep(300000);
            $user = User::query()->where('discord_name', $message['author']['username'])->first();
            $user_id = $user?->id;

            try {
                $this->discord->addReaction($message['id'], '👀');

                $lines = explode("\n", $message['content']);
                array_shift($lines);

                $parts = explode(',', $lines[0]);
                $parts = array_map('trim', $parts);

                $amount = 0;

                foreach ($parts as $part) {
                    $amount = PregMatchService::findKeyReturnFloat($part, config('units.price')) ?? $amount;
                }
                $subcategory_id = Subcategory::query()->where('name', $parts[0])->value('id');

                if ($subcategory_id) {
                    $income = new Income();
                    $income->user_id = $user_id;
                    $income->subcategory_id = $subcategory_id;
                    $income->amount = $amount;
                    $income->save();
                    $this->discord->addReaction($message['id'], '👍');
                    Log::channel('discord')->info('Income processed successfully: ' . $message['id']);
                } else {
                    $this->discord->addReaction($message['id'], '👎');
                    Log::channel('discord')->error('Income processing failed: ' . $message['id']);
                }
            } catch (\Exception $e) {
                $this->discord->addReaction($message['id'], '👎');
                Log::channel('discord')->error('Income processing failed: ' . $message['id'] . ': ' . $e->getMessage());
            }
        }
    }

    // TODO требуется донастройка метода!
    // Афи, инвестиции
    // 1010 руб - строка бел суммы чека
    // BTC, 0.002, 71291 - код, размер, цена за ед в $
    // ETH, 0.03, 3818 - код, размер, цена за ед в $
    // LTC, 0.6, 84.48 - код, размер, цена за ед в $
    public function processMessagesInvestment(array $messages)
    {
        foreach ($messages as $message) {
            usleep(300000);
            $user = User::query()->where('discord_name', $message['author']['username'])->first();
            $user_id = $user?->id;

            try {
                $this->discord->addReaction($message['id'], '👀');

                $lines = explode("\n", $message['content']);
                array_shift($lines);

                $parts = explode(',', $lines[0]);
                $parts = array_map('trim', $parts);

                $amount = 0;

                foreach ($parts as $part) {
                    $amount = PregMatchService::findKeyReturnFloat($part, config('units.price')) ?? $amount;
                }
                $subcategory_id = Subcategory::query()->where('name', $parts[0])->value('id');

                if ($subcategory_id) {
                    $income = new Income();
                    $income->user_id = $user_id;
                    $income->subcategory_id = $subcategory_id;
                    $income->amount = $amount;
                    $income->save();
                    $this->discord->addReaction($message['id'], '👍');
                    Log::channel('discord')->info('Income processed successfully: ' . $message['id']);
                } else {
                    $this->discord->addReaction($message['id'], '👎');
                    Log::channel('discord')->error('Income processing failed: ' . $message['id']);
                }
            } catch (\Exception $e) {
                $this->discord->addReaction($message['id'], '👎');
                Log::channel('discord')->error('Income processing failed: ' . $message['id'] . ': ' . $e->getMessage());
            }
        }
    }
}

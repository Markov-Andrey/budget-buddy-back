<?php

use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/hello', function () {
    $token = env('DISCORD_API_BOT_TOKEN');
    $client = new Client([
        'headers' => [
            'Authorization' => 'Bot ' . $token,
        ],
    ]);
    $emojiView = '👍';

    $response = $client->get('https://discord.com/api/v10/channels/1245678879151095861/messages');
    $messages = json_decode($response->getBody(), true);
    $messagesWithNoReaction = [];

    if (!empty($messages)) {
        foreach ($messages as $message) {
            $messageId = $message['id'];
            $hasThumbsUpReaction = false;

            // Проверяем, есть ли реакции у сообщения
            if (isset($message['reactions'])) {
                foreach ($message['reactions'] as $reaction) {
                    if ($reaction['me'] === true && $reaction['emoji']['name'] === $emojiView) {
                        $hasThumbsUpReaction = true;
                        break;
                    }
                }
            }

            // Если реакции "👍" нет, добавляем ее
            if (!$hasThumbsUpReaction) {
                $putResponse = $client->put("https://discord.com/api/v10/channels/1245678879151095861/messages/{$messageId}/reactions/{$emojiView}/@me");

                if ($putResponse->getStatusCode() == 204) {
                    $message['reaction_added'] = true;
                } else {
                    $message['reaction_added'] = false;
                }

                $messagesWithNoReaction[] = $message;
            }
        }
    }

    return dd($messagesWithNoReaction);
});

Route::get('/', function () {
    return redirect('/admin'); // базовый рероут
});

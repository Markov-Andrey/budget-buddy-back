<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class GeminiController extends Controller
{
    private static function sendRequest(array $requestBody): array
    {
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post(config('gemini.base_url') . '?key=' . config('gemini.api_key'), $requestBody);

        if ($response->failed()) {
            throw new \Exception("API request failed with status: " . $response->status());
        }

        return json_decode($response->body(), true);
    }

    public static function generateTextUsingImageFile($mimeType, $filePath, $prompt): string
    {
        $imageContent = base64_encode(file_get_contents($filePath));
        $requestBody = [
            'contents' => [[
                'parts' => [
                    ['text' => $prompt],
                    ['inline_data' => ['mime_type' => $mimeType, 'data' => $imageContent]]
                ]
            ]]
        ];

        $responseBody = self::sendRequest($requestBody);
        return $responseBody['candidates'][0]['content']['parts'][0]['text'] ?? 'No text found';
    }

    public static function generateText($prompt): string
    {
        $requestBody = [
            'contents' => [[
                'parts' => [['text' => $prompt]]
            ]]
        ];

        $responseBody = self::sendRequest($requestBody);
        return $responseBody['candidates'][0]['content']['parts'][0]['text'] ?? 'No text found';
    }
}

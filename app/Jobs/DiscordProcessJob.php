<?php

namespace App\Jobs;

use App\Models\Receipts;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\DiscordController;
use Illuminate\Support\Facades\Storage;

class DiscordProcessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $items = (new DiscordController)->index();
        foreach ($items as $item) {
            $user = User::where('discord_name', $item['author']['global_name'])->first();

            if ($user) {
                $userId = $user->id;

                if ($item['attachments']) {
                    foreach ($item['attachments'] as $attachment) {
                        // Извлечение имени файла и расширения с помощью регулярного выражения, очистка от параметров
                        if (preg_match('/\/([^\/?#]+)\.(png|jpe?g|gif)(?:$|\?|#)/i', $attachment['proxy_url'], $matches)) {
                            $originalFileName = $matches[1];
                            $extension = $matches[2];
                            $relativePath = 'public/receipts/' . $originalFileName . '.' . $extension;
                            $contents = file_get_contents($attachment['proxy_url']);

                            if ($contents !== false) {
                                Storage::put($relativePath, $contents);

                                Receipts::create([
                                    'user_id' => $userId,
                                    'image_path' => 'receipts/' . $originalFileName . '.' . $extension,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}

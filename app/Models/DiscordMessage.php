<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscordMessage extends Model
{
    protected $table = 'discord_messages';
    use HasFactory;

    protected $fillable = [
        'code',
        'message',
    ];

    /**
     * Получить одно случайное сообщение по коду.
     *
     * @param string $code Код сообщения
     * @return string|null Случайное сообщение или null, если сообщение с указанным кодом не найдено
     */
    public static function getRandomMessageByCode(string $code): ?string
    {
        return self::where('code', $code)->inRandomOrder()->value('message');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscordChat extends Model
{
    use HasFactory;

    protected $table = 'discord_chat';

    protected $fillable = [
        'chat_id',
        'last_message_id',
    ];
}

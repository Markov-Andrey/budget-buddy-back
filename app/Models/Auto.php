<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auto extends Model
{
    use HasFactory;

    protected $table = 'auto';

    protected $fillable = [
        'name',
        'user_id',
        'service_interval',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function allReceipts()
    {
        return $this->morphMany(ReceiptsData::class, 'morph');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipts extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'user_id',
        'processed',
        'error',
    ];

    protected $casts = [
        'processed' => 'boolean',
        'error' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

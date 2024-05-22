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
        'annulled',
    ];

    protected $casts = [
        'processed' => 'boolean',
        'error' => 'boolean',
        'annulled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function data()
    {
        return $this->hasMany(ReceiptsData::class);
    }

    public function address()
    {
        return $this->hasMany(ReceiptsOrganization::class);
    }
}

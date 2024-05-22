<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptsData extends Model
{
    use HasFactory;

    // Указываем, какие поля могут быть массово назначены
    protected $fillable = [
        'receipts_id',
        'name',
        'quantity',
        'weight',
        'price'
    ];

    // Определяем связь с моделью Receipt
    public function receipt()
    {
        return $this->belongsTo(Receipts::class, 'receipts_id');
    }
}

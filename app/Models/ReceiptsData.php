<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptsData extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipts_id',
        'name',
        'quantity',
        'weight',
        'price',
        'subcategory_id'
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipts::class, 'receipts_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }
}

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
        'subcategory_id',
        'morph_id',
        'morph_type',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipts::class, 'receipts_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }
    public function autoInsurance()
    {
        return $this->hasOne(AutoInsurance::class, 'receipts_data_id');
    }

    public function autoTechInspections()
    {
        return $this->hasOne(AutoTechnicalInspection::class, 'receipts_data_id');
    }

    public function morph()
    {
        return $this->morphTo();
    }

    public static function formattedData($item) {
        $name = $item->name ?? '-';
        $subcategoryName = $item->subcategory ? $item->subcategory->name : '-';
        $quantity = $item->quantity ?? '-';
        $price = $item->price ?? '-';
        $weight = $item->weight ?? '-';

        return "{$name} ({$subcategoryName}) - {$quantity} ({$weight}) - {$price}";
    }
}

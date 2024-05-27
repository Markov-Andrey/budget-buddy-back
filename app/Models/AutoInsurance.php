<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoInsurance extends Model
{
    use HasFactory;

    protected $table = 'auto_insurance';

    protected $fillable = [
        'receipts_data_id',
        'expiry_date',
    ];

    public function receiptsData()
    {
        return $this->belongsTo(ReceiptsData::class);
    }
}

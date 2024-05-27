<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoTechnicalInspection extends Model
{
    use HasFactory;

    protected $table = 'auto_technical_inspections';

    protected $fillable = [
        'receipts_data_id',
        'inspection_mileage',
    ];

    public function receiptsData()
    {
        return $this->belongsTo(ReceiptsData::class);
    }
}

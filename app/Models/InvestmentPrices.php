<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentPrices extends Model
{
    protected $table = 'investment_prices';
    protected $fillable = [
        'investment_type_id',
        'date',
        'price',
        'currency_type',
    ];
    public $timestamps = false;

    public function investmentType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvestmentType::class);
    }
}

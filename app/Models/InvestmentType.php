<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentType extends Model
{
    protected $table = 'investment_types';
    protected $fillable = [
        'name',
        'code',
        'coingecko_id',
        'nbrb_id',
    ];

    public $timestamps = false;

    public function investmentPrices()
    {
        return $this->hasMany(InvestmentPrices::class, 'investment_type_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentPrices extends Model
{
    public function investmentType()
    {
        return $this->belongsTo(InvestmentType::class);
    }
}

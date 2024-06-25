<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentDetails extends Model
{
    protected $table = 'investment_details';
    protected $fillable = [
        'investment_id',
        'investment_type_id',
        'size',
        'cost_per_unit',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function investmentType()
    {
        return $this->belongsTo(InvestmentType::class);
    }
}

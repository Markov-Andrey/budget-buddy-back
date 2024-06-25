<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $table = 'investments';
    protected $fillable = ['user_id', 'total_amount'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function investmentDetail(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->HasMany(InvestmentDetails::class);
    }

    public function setTotalAmountAttribute($value): void
    {
        $this->attributes['total_amount'] = $value * 100;
    }

    public function getTotalAmountAttribute($value): float|int
    {
        return $value / 100;
    }
}

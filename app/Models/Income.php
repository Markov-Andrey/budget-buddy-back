<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $table = 'income';

    protected $fillable = [
        'user_id',
        'subcategory_id',
        'amount',
    ];

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

    public function getAmountAttribute($value)
    {
        return $value / 100;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public static function totalIncomeUser($userId)
    {
        $incomes = self::where('user_id', $userId)->get();
        $totalIncome = 0;
        foreach ($incomes as $income) {
            $totalIncome += $income->amount;
        }

        return $totalIncome;
    }

    public static function calculateByCategory($userId)
    {
        $incomes = self::query()->where('user_id', $userId)->with('subcategory')->get();
        $details = [];
        $total = 0;

        foreach ($incomes as $income) {
            $subcategoryName = $income->subcategory->name;
            if (isset($details[$subcategoryName])) {
                $details[$subcategoryName] += $income->amount;
            } else {
                $details[$subcategoryName] = $income->amount;
            }
            $total += $income->amount;
        }

        return [
            'details' => $details,
            'total' => $total,
        ];
    }
}

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
        return self::query()->where('user_id', $userId)->sum('amount') / 100;
    }

    public static function calculateByCategory($userId)
    {
        $userIds = is_array($userId) ? $userId : [$userId];

        $incomes = self::query()
            ->whereIn('user_id', $userIds)
            ->with('subcategory')
            ->get();
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

    /**
     * Получить среднемесячный доход за последний год для указанного пользователя
     *
     * @param int $userId
     * @return float
     */
    public static function averageMonthlyLastYear($userId): float
    {
        $userIds = is_array($userId) ? $userId : [$userId];

        // Получаем текущую дату и дату, предшествующую году
        $now = now();
        $oneYearAgo = $now->subYear();

        // Выполняем запрос
        $averageIncome = self::query()
            ->whereIn('user_id', $userIds)
            ->where('created_at', '>=', $oneYearAgo)
            ->sum('amount');

        // Получаем количество месяцев данных (минимум 1 месяц)
        $monthsWithData = max(1, $now->diffInMonths($oneYearAgo));

        // Рассчитываем средний доход за месяц
        $averageMonthlyIncome = $averageIncome / $monthsWithData;

        // Применяем мутатор для преобразования суммы
        return (new Income)->getAmountAttribute($averageMonthlyIncome);
    }
}

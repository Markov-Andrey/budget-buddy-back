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

    /**
     * Получить среднемесячный доход за последний год для указанного пользователя
     * с отбраковкой определенных субкатегорий.
     *
     * @param int $user_id
     * @return float
     */
    public static function averageMonthlyIncomeLastYear(int $user_id): float
    {
        // Субкатегории, которые необходимо исключить из расчета
        $subcategoriesToExclude = ['Разовый доход', 'Продажа б/у'];

        // Получаем текущую дату и дату, предшествующую году
        $now = now();
        $oneYearAgo = $now->subYear();

        // Выполняем запрос
        $averageIncome = self::where('user_id', $user_id)
            ->whereNotIn('subcategory_id', function ($query) use ($subcategoriesToExclude) {
                // Подзапрос для выбора ID субкатегорий, которые нужно исключить
                $query->select('id')
                    ->from('subcategories')
                    ->whereIn('name', $subcategoriesToExclude);
            })
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

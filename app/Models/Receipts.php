<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipts extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'user_id',
        'processed',
        'error',
        'annulled',
        'amount',
        'datetime',
    ];

    protected $casts = [
        'processed' => 'boolean',
        'error' => 'boolean',
        'annulled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function data()
    {
        return $this->hasMany(ReceiptsData::class);
    }

    public function address()
    {
        return $this->hasMany(ReceiptsOrganization::class);
    }

    public static function totalLossUser($userId)
    {
        $incomes = self::where('user_id', $userId)->get();
        $totalIncome = 0;
        foreach ($incomes as $income) {
            $totalIncome += $income->amount;
        }

        return $totalIncome;
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

    public function getAmountAttribute($value)
    {
        return $value / 100;
    }

    public static function calculatePricesByCategory($userId, $categoryIdentifier = null, $sort = true)
    {
        $query = Receipts::with('data.subcategory.category')
            ->where('user_id', $userId);

        // Если задан идентификатор категории, фильтруем по нему
        if ($categoryIdentifier !== null) {
            $query->whereHas('data.subcategory.category', function ($query) use ($categoryIdentifier) {
                if (is_numeric($categoryIdentifier)) {
                    $query->where('id', $categoryIdentifier);
                } else {
                    $query->where('name', $categoryIdentifier);
                }
            });
        }

        // Получение данных
        $receipts = $query->first();

        // Инициализация массива для результатов
        $details = [];
        $dataTotal = 0;

        // Обработка данных
        if ($receipts) {
            foreach ($receipts->data as $item) {
                $price = str_replace(',', '.', $item->price);
                $quantity = $item->quantity;
                if (is_numeric($price) && is_numeric($quantity)) {
                    $totalPrice = $price * $quantity;
                    $subcategoryName = $item->subcategory->name;
                    $categoryName = $item->subcategory->category->name;

                    // Если указано имя категории или ID категории, собираем данные о продуктах
                    if ($categoryIdentifier !== null) {
                        if (($categoryIdentifier === $categoryName) || (is_numeric($categoryIdentifier) && $item->subcategory->category->id == $categoryIdentifier)) {
                            if (!isset($details[$subcategoryName])) {
                                $details[$subcategoryName] = 0;
                            }
                            $details[$subcategoryName] += $totalPrice;
                            $dataTotal += $totalPrice;
                        }
                    } else { // Иначе собираем данные о категориях
                        if (!isset($details[$categoryName])) {
                            $details[$categoryName] = 0;
                        }
                        $details[$categoryName] += $totalPrice;
                        $dataTotal += $totalPrice;
                    }
                }
            }
        }

        if ($sort) {
            arsort($details);
        }

        // Возвращаем результаты
        return [
            'details' => $details,
            'total' => $dataTotal
        ];
    }
}

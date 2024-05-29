<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *      schema="Receipt",
 *      title="Receipt",
 *      description="Receipt object",
 *      @OA\Property(
 *          property="id",
 *          description="ID of the receipt",
 *          type="integer",
 *          example=1
 *      ),
 *      @OA\Property(
 *          property="image_path",
 *          description="Path to the image of the receipt",
 *          type="string",
 *          example="/path/to/image.jpg"
 *      ),
 *      @OA\Property(
 *          property="user_id",
 *          description="ID of the user who uploaded the receipt",
 *          type="integer",
 *          example=1
 *      ),
 *      @OA\Property(
 *          property="processed",
 *          description="Flag indicating whether the receipt has been processed",
 *          type="boolean",
 *          example=true
 *      ),
 *      @OA\Property(
 *          property="error",
 *          description="Flag indicating whether an error occurred during processing",
 *          type="boolean",
 *          example=false
 *      ),
 *      @OA\Property(
 *          property="annulled",
 *          description="Flag indicating whether the receipt has been annulled",
 *          type="boolean",
 *          example=false
 *      ),
 *      @OA\Property(
 *          property="amount",
 *          description="Amount of the receipt",
 *          type="number",
 *          example=100.50
 *      ),
 *      @OA\Property(
 *          property="datetime",
 *          description="Date and time when the receipt was created",
 *          type="string",
 *          format="date-time",
 *          example="2024-05-30T12:00:00Z"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="Date and time when the receipt was created",
 *          type="string",
 *          format="date-time",
 *          example="2024-05-30T12:00:00Z"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="Date and time when the receipt was last updated",
 *          type="string",
 *          format="date-time",
 *          example="2024-05-30T12:00:00Z"
 *      )
 * )
 */
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
        return self::query()->where('user_id', $userId)->sum('amount') / 100;
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

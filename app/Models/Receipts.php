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
        $query = ReceiptsData::query()
            ->join('receipts', 'receipts.id', '=', 'receipts_data.receipts_id')
            ->join('subcategories', 'receipts_data.subcategory_id', '=', 'subcategories.id')
            ->join('categories', 'subcategories.category_id', '=', 'categories.id')
            ->selectRaw('categories.name AS category_name, SUM(receipts_data.price * receipts_data.quantity) AS total_price')
            ->where('receipts.user_id', $userId)
            ->groupBy('categories.name');

        // Если задан идентификатор категории, фильтруем по нему
        if ($categoryIdentifier !== null) {
            if (is_numeric($categoryIdentifier)) {
                $query->where('categories.id', $categoryIdentifier);
            } else {
                $query->where('categories.name', $categoryIdentifier);
            }
        }

        // Получение данных
        $details = $query->get()->toArray();

        // Преобразование формата данных
        $formattedDetails = [];
        foreach ($details as $detail) {
            $formattedDetails[$detail['category_name']] = $detail['total_price'];
        }

        // Сортировка, если требуется
        if ($sort) {
            arsort($formattedDetails);
        }

        return [
            'details' => $formattedDetails,
            'total' => array_sum($formattedDetails)
        ];
    }
}

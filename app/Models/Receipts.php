<?php

namespace App\Models;

use App\Services\DateService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
            ->whereNot('receipts.annulled', '=', 1)
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

        // Если задана конкретная категория, собираем данные по субкатегориям
        if ($categoryIdentifier !== null && !is_numeric($categoryIdentifier)) {
            $subcategoryDetails = self::calculatePricesBySubcategory($userId, $categoryIdentifier);
            return [
                'details' => $subcategoryDetails,
                'total' => array_sum($subcategoryDetails)
            ];
        }

        return [
            'details' => $formattedDetails,
            'total' => array_sum($formattedDetails)
        ];
    }

    public static function calculatePricesBySubcategory($userId, $categoryName, $sort = true)
    {
        $query = ReceiptsData::query()
            ->join('receipts', 'receipts.id', '=', 'receipts_data.receipts_id')
            ->join('subcategories', 'receipts_data.subcategory_id', '=', 'subcategories.id')
            ->join('categories', 'subcategories.category_id', '=', 'categories.id')
            ->selectRaw('subcategories.name AS subcategory_name, SUM(receipts_data.price * receipts_data.quantity) AS total_price')
            ->whereNot('receipts.annulled', '=', 1)
            ->where('receipts.user_id', $userId)
            ->where('categories.name', $categoryName)
            ->groupBy('subcategories.name');

        // Получение данных
        $details = $query->get()->toArray();

        // Преобразование формата данных
        $formattedDetails = [];
        foreach ($details as $detail) {
            $formattedDetails[$detail['subcategory_name']] = $detail['total_price'];
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

    /**
     * Получить среднемесячные траты за последний год для указанного пользователя по чекам
     *
     * @param int $user_id
     * @return float
     */
    public static function averageMonthlyLastYear($user_id): float
    {
        // Получаем текущую дату и дату, предшествующую году
        $now = now();
        $oneYearAgo = $now->subYear();

        // Выполняем запрос для получения суммы дохода за последний год
        $averageIncome = self::where('user_id', $user_id)
            ->where('created_at', '>=', $oneYearAgo)
            ->sum('amount');

        // Получаем количество месяцев данных (минимум 1 месяц)
        $monthsWithData = max(1, $now->diffInMonths($oneYearAgo));
        $averageMonthlyIncome = $averageIncome / $monthsWithData;

        return (new Receipts)->getAmountAttribute($averageMonthlyIncome);
    }

    /**
    * Параметр DE - реальный расход в день.
    * Вернуть расходы по id пользователя по дням месяца
    */
    public static function amountByDayFromDate($user_id, $date = null): array
    {
        $monthRange = DateService::monthRange($date);
        $amountByDay = self::where('user_id', $user_id)
            ->whereBetween('datetime', [$monthRange['start'], $monthRange['end']])
            ->selectRaw('DAY(datetime) as day, CAST(SUM(amount / 100) AS DECIMAL(10,2)) as total_amount')
            ->groupBy('day')
            ->pluck('total_amount', 'day')
            ->toArray();

        $amountByDay = array_replace(array_fill(1, $monthRange['count'], 0), $amountByDay);
        $amountByDay = array_combine(range(0, $monthRange['count'] - 1), $amountByDay);

        ksort($amountByDay);

        return $amountByDay;
    }

    /**
     * Параметр CEA - накопление расходов в течение месяца.
     * Вернуть расходы по id пользователя по дням месяца по накопительной системе
     */
    public static function cumulativeAmountByDayFromDate($user_id, $date = null): array
    {
        $amountByDay = self::amountByDayFromDate($user_id, $date);
        $currentDate = Carbon::parse($date)->endOfDay();

        $cumulativeAmount = 0;

        foreach ($amountByDay as $day => $amount) {
            $dayDate = Carbon::parse($currentDate)->startOfMonth()->addDays($day);

            if ($dayDate > $currentDate) {
                $cumulativeAmount = null;
            } else {
                $cumulativeAmount += $amount;
            }

            $amountByDay[$day] = $cumulativeAmount;
        }

        return $amountByDay;
    }
}

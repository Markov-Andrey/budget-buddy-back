<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * Получить все инвестиции по пользователям для таблицы
     * @param $userId
     * @return mixed
     */
    public static function getInvestmentDetailsData($userId): mixed
    {
        $userIds = is_array($userId) ? $userId : [$userId];

        // Получить агрегированные данные
        $aggregatedData = InvestmentDetails::whereHas('investment', function ($query) use ($userIds) {
            $query->whereIn('user_id', $userIds);
        })
            ->select(
                'investment_type_id',
                DB::raw('TRIM(TRAILING "." FROM TRIM(TRAILING "0" FROM SUM(size * cost_per_unit))) as total_value'),
                DB::raw('TRIM(TRAILING "." FROM TRIM(TRAILING "0" FROM TRIM(TRAILING "0" FROM SUM(size)))) as total_size'),
                DB::raw('TRIM(TRAILING "." FROM TRIM(TRAILING "0" FROM TRIM(TRAILING "0" FROM SUM(CASE WHEN size >= 0 THEN size * cost_per_unit ELSE 0 END) / NULLIF(SUM(CASE WHEN size >= 0 THEN size ELSE 0 END), 0)))) AS average_cost_per_unit'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('investment_type_id')
            ->get();

        // Получить информацию о каждом investment_type_id
        $investmentTypes = InvestmentType::whereIn('id', $aggregatedData->pluck('investment_type_id'))->get()->keyBy('id');

        // Получить последнюю запись из InvestmentPrices для каждого InvestmentType
        $latestPrices = InvestmentPrices::select('investment_type_id', 'date', 'price')
            ->whereIn('investment_type_id', $aggregatedData->pluck('investment_type_id'))
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('investment_type_id');

        // Объединить данные
        $result = $aggregatedData->map(function ($item) use ($investmentTypes, $latestPrices) {
            // Форматирование average_cost_per_unit (если не целое, то округляем до 2 знаков после запятой)
            $average_cost_per_unit = $item->average_cost_per_unit;
            if ($average_cost_per_unit > 10) {
                $average_cost_per_unit = number_format($average_cost_per_unit, 2, '.', '');
            }

            $total_value = $item->total_size * $item->average_cost_per_unit;
            $latestPriceData = isset($latestPrices[$item->investment_type_id]) ? $latestPrices[$item->investment_type_id]->first() : null;
            $latestDate = $latestPriceData ? Carbon::parse($latestPriceData->date)->format('d.m.y') : null;
            $latestPrice = $latestPriceData ? self::formatPrice($latestPriceData->price) : null;
            $latest_amount = $latestPrice ? $latestPrice * $item->total_size : null;
            $latest_percent = $latest_amount ? ($latest_amount - $total_value) / $total_value * 100 : null;

            return [
                'investment_type_id' => $item->investment_type_id,
                'total_value' => round($total_value, 2),
                'total_size' => $item->total_size,
                'average_cost_per_unit' => self::formatPrice($average_cost_per_unit),
                'investment_type_name' => $investmentTypes[$item->investment_type_id]->name ?? '',
                'investment_type_code' => $investmentTypes[$item->investment_type_id]->code ?? '',
                'latest_price_date' => $latestDate,
                'latest_price' => $latestPrice,
                'latest_amount' => round($latest_amount, 2),
                'latest_percent' => round($latest_percent, 2),
            ];
        });
        $sortedResult = $result->sortByDesc('total_value');

        return $sortedResult->values()->toArray();
    }

    protected static function formatPrice($price)
    {
        if ($price > 1) {
            return number_format($price, 2, '.', '');
        } elseif ($price < 1 && $price > 0) {
            // Convert the number to a string
            $priceStr = number_format($price, 8, '.', '');

            // Find the first non-zero digit after the decimal point
            $decimalPart = substr($priceStr, strpos($priceStr, '.') + 1);
            $nonZeroPos = strspn($decimalPart, '0');

            // Get the first three significant digits
            $significantPart = substr($decimalPart, $nonZeroPos, 3);
            $formattedPrice = '0.' . str_repeat('0', $nonZeroPos) . $significantPart;

            return rtrim(rtrim($formattedPrice, '0'), '.');
        }

        return $price;
    }
}

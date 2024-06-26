<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public static function getInvestmentDetailsData($userId)
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
                DB::raw('TRIM(TRAILING "." FROM TRIM(TRAILING "0" FROM TRIM(TRAILING "0" FROM SUM(size * cost_per_unit) / SUM(size)))) AS average_cost_per_unit'),
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

            $latestPriceData = $latestPrices[$item->investment_type_id]->first() ?? null;
            $latestDate = $latestPriceData ? Carbon::parse($latestPriceData->date)->format('d.m.y') : null;
            $latestPrice = $latestPriceData ? self::formatPrice($latestPriceData->price) : null;
            $latest_amount = $latestPrice ? $latestPrice * $item->total_size : null;
            $latest_percent = $latest_amount ? ($latest_amount - $item->total_value) / $item->total_value * 100 : null;

            return [
                'investment_type_id' => $item->investment_type_id,
                'total_value' => round($item->total_value, 2),
                'total_size' => $item->total_size,
                'average_cost_per_unit' => $average_cost_per_unit,
                'investment_type_name' => $investmentTypes[$item->investment_type_id]->name ?? '',
                'investment_type_code' => $investmentTypes[$item->investment_type_id]->code ?? '',
                'latest_price_date' => $latestDate,
                'latest_price' => $latestPrice,
                'latest_amount' => round($latest_amount, 2),
                'latest_percent' => round($latest_percent, 2),
            ];
        });

        return $result->toArray();
    }

    protected static function formatPrice($price)
    {
        if ($price > 1) {
            return number_format($price, 2, '.', '');
        } elseif ($price < 1) {
            // Преобразование числа в строку и обрезка до 3 значащих цифр после последнего ненулевого знака
            $priceStr = rtrim(rtrim(number_format($price, 8, '.', ''), '0'), '.');
            $parts = explode('.', $priceStr);

            if (count($parts) == 2) {
                $decimalPart = substr($parts[1], 0, 3); // Берем первые 3 цифры после запятой
                $priceStr = $parts[0] . '.' . $decimalPart;
            }

            return rtrim(rtrim($priceStr, '0'), '.');
        }

        return $price;
    }
}

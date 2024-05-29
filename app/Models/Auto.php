<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auto extends Model
{
    use HasFactory;

    protected $table = 'auto';

    protected $fillable = [
        'name',
        'user_id',
        'service_interval',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function allReceipts()
    {
        return $this->morphMany(ReceiptsData::class, 'morph');
    }

    public static function getAutoDataByUserId($userId)
    {
        $auto = Auto::with('allReceipts', 'allReceipts.subcategory', 'allReceipts.receipt')
            ->where('user_id', $userId)
            ->get()
            ->toArray();

        $autoData = [];
        foreach ($auto as $car) {
            $totalFuel = 0;
            $totalDays = 0;
            $previousReceipt = null;
            $receipts = [];
            foreach ($car['all_receipts'] as $receipt) {
                if (isset($receipt['receipt']) && isset($receipt['subcategory'])) {
                    $receipts[] = [
                        'datetime' => $receipt['receipt']['datetime'],
                        'subcategory' => $receipt['subcategory']['name'],
                        'weight' => $receipt['weight'],
                        'fuel_name' => $receipt['name'],
                    ];
                }
                if ($previousReceipt) {
                    $daysDifference = strtotime($receipt['receipt']['datetime']) - strtotime($previousReceipt['receipt']['datetime']);
                    if ($daysDifference > 0) {
                        $daysDifference = floor($daysDifference / (60 * 60 * 24)); // Количество дней между заправками
                        $totalFuel += $receipt['weight']; // Суммируем общее количество топлива
                        $totalDays += $daysDifference; // Суммируем общее количество дней
                    }
                }
                $previousReceipt = $receipt;
            }
            // Вычисляем средний расход для данного автомобиля
            $averageConsumption = $totalDays > 0 ? $totalFuel / $totalDays : 0;

            // Вычисляем разницу в днях между первой и последней заправками
            $firstDate = isset($car['all_receipts'][0]['receipt']['datetime']) ? $car['all_receipts'][0]['receipt']['datetime'] : null;
            $lastDate = isset($receipt['receipt']['datetime']) ? $receipt['receipt']['datetime'] : null;
            $dateDifference = 0;
            if ($firstDate && $lastDate) {
                $firstDateCarbon = Carbon::parse($firstDate);
                $lastDateCarbon = Carbon::parse($lastDate);
                $dateDifference = $firstDateCarbon->diffInDays($lastDateCarbon);
            }

            $autoData[] = [
                'car_name' => $car['name'],
                'receipts' => $receipts,
                'average_consumption' => $averageConsumption,
                'first_date' => $firstDate,
                'last_date' => $lastDate,
                'date_difference' => $dateDifference,
            ];
        }

        return $autoData;
    }
}

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
        $userIds = is_array($userId) ? $userId : [$userId];

        $auto = Auto::with('allReceipts', 'allReceipts.subcategory', 'allReceipts.receipt', 'allReceipts.autoInsurance', 'allReceipts.autoTechInspections')
            ->whereIn('user_id', $userIds)
            ->get()
            ->toArray();

        $autoData = [];
        foreach ($auto as $car) {
            $receiptFuel = self::getReceiptFuel($car['all_receipts']);
            $receiptInsurances = self::getReceiptInsurances($car['all_receipts']);
            $receiptTechInspections = self::getReceiptTechInspection($car['all_receipts'], $car['service_interval']);
            $result = self::calculateAverageConsumptionAndDates($car['all_receipts']);

            $autoData[] = [
                'car_name' => $car['name'],
                'average_consumption' => $result['average_consumption'],
                'first_date' => $result['first_date'],
                'last_date' => $result['last_date'],
                'date_difference' => $result['date_difference'],
                'receiptFuel' => $receiptFuel,
                'receiptInsurances' => $receiptInsurances,
                'receiptTechInspections' => $receiptTechInspections,
            ];
        }

        return $autoData;
    }

    private static function getReceiptFuel(array $receipts)
    {
        $receiptFuel = [];
        foreach ($receipts as $receipt) {
            if (isset($receipt['receipt']) && $receipt['subcategory'] && $receipt['subcategory']['name'] === 'Топливо') {
                $formattedDate = Carbon::parse($receipt['receipt']['datetime'])->format('d.m.y');
                $receiptFuel[] = [
                    'datetime' => $formattedDate,
                    'subcategory' => $receipt['subcategory']['name'],
                    'weight' => $receipt['weight'],
                    'fuel_name' => $receipt['name'],
                ];
            }
        }
        return $receiptFuel;
    }

    private static function getReceiptInsurances(array $receipts)
    {
        $receiptInsurances = [];
        $now = now();
        foreach ($receipts as $receipt) {
            if (
                isset($receipt['receipt'])
                && $receipt['subcategory']
                && $receipt['subcategory']['name'] === 'Страховка'
                && isset($receipt['auto_insurance']['expiry_date'])
            ) {
                $expiryDate = Carbon::parse($receipt['auto_insurance']['expiry_date']);
                if ($expiryDate->gt($now)) {
                    $formattedDate = Carbon::parse($receipt['receipt']['datetime'])->format('d.m.y');
                    $formattedExpiryDate = $expiryDate->format('d.m.y');
                    $receiptInsurances[] = [
                        'datetime' => $formattedDate,
                        'subcategory' => $receipt['subcategory']['name'],
                        'expiry_date' => $formattedExpiryDate,
                    ];
                }
            }
        }
        return $receiptInsurances;
    }

    private static function getReceiptTechInspection(array $receipts, $serviceInterval = 0)
    {
        $receiptInsurances = [];
        foreach ($receipts as $receipt) {
            if (isset($receipt['receipt']) && $receipt['subcategory'] && $receipt['subcategory']['name'] === 'Плановое ТО') {
                $formattedDate = Carbon::parse($receipt['receipt']['datetime'])->format('d.m.y');
                $nextTechInspection = $receipt['auto_tech_inspections']['inspection_mileage'] + $serviceInterval;
                $receiptInsurances[] = [
                    'datetime' => $formattedDate,
                    'name' => $receipt['name'],
                    'quantity' => $receipt['quantity'],
                    'weight' => $receipt['weight'],
                    'price' => $receipt['price'],
                    'subcategory' => $receipt['subcategory']['name'],
                    'techInspection' => $receipt['auto_tech_inspections']['inspection_mileage'],
                    'nextTechInspection' => $nextTechInspection,
                ];
            }
        }
        return $receiptInsurances;
    }

    private static function calculateAverageConsumptionAndDates(array $receipts)
    {
        $totalFuel = 0;
        $totalDays = 0;
        $previousReceipt = null;

        foreach ($receipts as $receipt) {
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
        $firstDate = $receipts[0]['receipt']['datetime'] ?? null;
        $lastDate = end($receipts)['receipt']['datetime'] ?? null;
        $dateDifference = 0;
        if ($firstDate && $lastDate) {
            $firstDateCarbon = Carbon::parse($firstDate);
            $lastDateCarbon = Carbon::parse($lastDate);
            $dateDifference = $firstDateCarbon->diffInDays($lastDateCarbon);
        }

        return [
            'average_consumption' => $averageConsumption,
            'first_date' => $firstDate,
            'last_date' => $lastDate,
            'date_difference' => $dateDifference,
        ];
    }
}

<?php

namespace App\Models;

use App\Services\DateService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReceiptsData extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipts_id',
        'name',
        'quantity',
        'weight',
        'price',
        'subcategory_id',
        'morph_id',
        'morph_type',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipts::class, 'receipts_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }
    public function autoInsurance()
    {
        return $this->hasOne(AutoInsurance::class, 'receipts_data_id');
    }

    public function autoTechInspections()
    {
        return $this->hasOne(AutoTechnicalInspection::class, 'receipts_data_id');
    }

    public function morph()
    {
        return $this->morphTo();
    }

    public static function topItems($user_id, $date = null, $max = true)
    {
        // Получаем диапазон месяца
        $monthRange = DateService::monthRange($date);
        $startOfMonth = $monthRange['start'];
        $endOfMonth = $monthRange['end'];

        // Определяем сортировку для запроса
        $orderDirection = $max ? 'DESC' : 'ASC';

        $query = DB::table('receipts_data')
            ->select('receipts_data.name', 'receipts_data.price', 'receipts_data.weight')
            ->join('receipts', 'receipts.id', '=', 'receipts_data.receipts_id')
            ->where('receipts.user_id', $user_id)
            ->whereBetween('receipts.datetime', [$startOfMonth, $endOfMonth])
            ->whereNotNull('receipts_data.price') // Фильтр на записи с ценой
            ->orderBy('receipts_data.price', $orderDirection)
            ->limit(10);

        $topItems = $query->get();

        // Преобразуем коллекцию к обычному массиву
        $topItemsArray = $topItems->toArray();

        // Вручную сортируем массив по полю "price"
        usort($topItemsArray, function ($a, $b) use ($orderDirection) {
            return $orderDirection == 'DESC' ? $b->price <=> $a->price : $a->price <=> $b->price;
        });

        // Преобразуем отсортированный массив обратно в коллекцию
        return new Collection($topItemsArray);
    }

    public static function formattedData($item) {
        $name = $item->name ?? '-';
        $subcategoryName = $item->subcategory ? $item->subcategory->name : '-';
        $quantity = $item->quantity ?? '-';
        $price = $item->price ?? '-';
        $weight = $item->weight ?? '-';

        return "{$name} ({$subcategoryName}) - {$quantity} ({$weight}) - {$price}";
    }
}

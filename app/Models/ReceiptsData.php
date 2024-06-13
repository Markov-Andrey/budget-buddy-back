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

    public static function topItems($user_id, $date = null, $max = true, $limit = 10)
    {
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
            ->whereNotNull('receipts_data.price')
            ->orderByRaw('CAST(receipts_data.price AS DECIMAL(10, 2)) ' . $orderDirection)
            ->limit($limit);

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

    /**
     * @param $user_id
     * @param $date
     * @return Collection
     * Метод сборки данных в кучку если совпадение >50%
     * TODO требуется доработать
     */
    public static function getGroupedItemsByMonth($user_id, $date = null)
    {
        // Получаем диапазон дат для указанного месяца
        $monthRange = DateService::monthRange($date);
        $startOfMonth = $monthRange['start'];
        $endOfMonth = $monthRange['end'];

        // Выполняем запрос для получения всех записей за указанный период
        $query = DB::table('receipts_data')
            ->join('receipts', 'receipts.id', '=', 'receipts_data.receipts_id')
            ->where('receipts.user_id', $user_id)
            ->whereBetween('receipts.datetime', [$startOfMonth, $endOfMonth])
            ->whereNotNull('receipts_data.price');

        $items = $query->get();

        // Группируем записи по имени и суммируем их значения
        $groupedItems = [];

        foreach ($items as $item) {
            $itemAdded = false;

            foreach ($groupedItems as &$group) {
                similar_text($group['name'], $item->name, $percent);

                // Используем порог в 50% схожести
                if ($percent > 50) {
                    // Найдем общий префикс
                    $commonPrefix = self::getCommonPrefix($group['name'], $item->name);

                    // Обновляем имя группы, если длина общего префикса больше минимальной длины (например, 3 символа)
                    if (mb_strlen($commonPrefix) >= 3) {
                        $group['name'] = $commonPrefix;
                    }

                    $group['total_price'] += (float) $item->price;
                    $group['total_weight'] += $item->weight;
                    $group['count'] += 1;
                    $itemAdded = true;
                    break;
                }
            }

            if (!$itemAdded) {
                $groupedItems[] = [
                    'name' => $item->name,
                    'total_price' => (float) $item->price,
                    'total_weight' => $item->weight,
                    'count' => 1,
                ];
            }
        }

        // Преобразуем сгруппированные элементы в коллекцию
        return collect($groupedItems);
    }

    public static function formattedData($item) {
        $name = $item->name ?? '-';
        $subcategoryName = $item->subcategory ? $item->subcategory->name : '-';
        $quantity = $item->quantity ?? '-';
        $price = $item->price ?? '-';
        $weight = $item->weight ?? '-';

        return "{$name} ({$subcategoryName}) - {$quantity} ({$weight}) - {$price}";
    }

    private static function getCommonPrefix($str1, $str2)
    {
        $len1 = mb_strlen($str1);
        $len2 = mb_strlen($str2);
        $minLen = min($len1, $len2);
        $prefix = '';

        for ($i = 0; $i < $minLen; $i++) {
            if (mb_substr($str1, $i, 1) === mb_substr($str2, $i, 1)) {
                $prefix .= mb_substr($str1, $i, 1);
            } else {
                break;
            }
        }

        return $prefix;
    }
}

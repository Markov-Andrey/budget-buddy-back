<?php

namespace App\Services;

use Carbon\Carbon;

class DateService
{
    /**
     * вернуть первый и последний дни месяца из даты
     * если $date пуст используется текущий месяц
     * start - метка первого дня месяца
     * end - метка последнего дня месяца
     * count - количество дней в месяце
     */
    public static function monthRange($date = null): array
    {
        $date = $date ?? now()->toDateString();

        return [
            'start' => Carbon::parse($date)->startOfMonth(),
            'end' => Carbon::parse($date)->endOfMonth(),
            'count' => Carbon::parse($date)->endOfMonth()->day,
        ];
    }
}

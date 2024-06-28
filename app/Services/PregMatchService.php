<?php

namespace App\Services;

class PregMatchService
{
    /**
     * Поиск в строке ключа и возвращение значения без ключа
     * @param string $string - строка по которой проиходит поиск
     * @param array $keys - массив ключей по которым идет поиск
     * @return string|null
     */
    public static function findKeyReturnFloat(string $string, array $keys): ?float
    {
        $pattern = '/(?<!\w)(' . implode('|', $keys) . ')(?!\w)/ui';
        if (preg_match($pattern, $string, $matches)) {
            $result = str_replace($matches[1], '', $string);
            return (float) trim($result);
        }

        return null;
    }
}

<?php

namespace App\Services;

class ReceiptProcessingService
{
    public static function getInfo(string $response)
    {
        $json = self::toJson($response);
        $validateJson = self::validate($json);
        $error = self::hasError($validateJson);

        return [
            'data' => $validateJson,
            'error' => $error,
        ];
    }

    private static function toJson($string)
    {
        $startPos = strpos($string, '{');
        $endPos = strrpos($string, '}');

        if ($startPos !== false && $endPos !== false) {
            $jsonSubstring = substr($string, $startPos, $endPos - $startPos + 1);
            $json = json_decode($jsonSubstring, true);
            return $json;
        }

        return null;
    }

    private static function validate($response)
    {
        $defaultStructure = config('api.default_structure');

        if (!is_array($response)) {
            $response = [];
        }

        foreach ($defaultStructure as $key => $defaultValue) {
            if (!array_key_exists($key, $response)) {
                $response[$key] = $defaultValue;
            } elseif (is_array($defaultValue)) {
                $response[$key] = self::validateArray($response[$key], $defaultValue);
            }
        }

        return $response;
    }

    private static function validateArray($array, $defaultArray)
    {
        if (!is_array($array) || empty($array)) {
            return $defaultArray;
        }

        if (array_keys($defaultArray) !== range(0, count($defaultArray) - 1)) {
            foreach ($defaultArray as $key => $defaultValue) {
                if (!array_key_exists($key, $array)) {
                    $array[$key] = $defaultValue;
                } elseif (is_array($defaultValue)) {
                    $array[$key] = self::validateArray($array[$key], $defaultValue);
                }
            }
        } else {
            foreach ($array as &$item) {
                $item = self::validateArray($item, $defaultArray[0]);
            }
        }

        return $array;
    }

    private static function hasError($array)
    {
        if (!$array || !is_array($array)) {
            return true;
        }
        foreach ($array as $value) {
            if (is_array($value)) {
                if (self::hasError($value)) {
                    return true;
                }
            } elseif ($value === null) {
                return true;
            }
        }
        return false;
    }
}

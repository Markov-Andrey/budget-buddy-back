<?php

return [
    'prompts' => [
        'check_processing' => '
            Твоя роль - анализ данных и ответ СТРОГО в формате JSON.
            Обработай фотографию чека и сформируй результат в виде JSON с данными, нужно выполнить следующие шаги:
            Найти наименование организации (organization).
            Сформируй массив адрес из полей - город (city), улица (street), дом-подъезд (entrance - объедини строку дома и подъезда).
            Извлеки и осмысли информацию о каждом наименовании товара, его количество, цену и вес (приведи в кг, если не нашел - поставь 0) в массив (name/quantity/price/weight).
            Найти общую стоимость товаров (totalPrice).
            В случае если данные не найдены или представляют неправильный формат в значении заполни значением NULL.
            ПРИМЕР результата:
            {
                "organization": "Санта Ритейл",
                "address": {
                    "city": "Минск",
                    "street": "Володько",
                    "entrance": "9, пом. 6"
                },
                "items": [
                    {
                        "name": "Карамель Чупа Чупс Ассорти",
                        "quantity": 1,
                        "weight": 0.012,
                        "price": 0.53
                    },
                    {
                        "name": "Коктейль молочный ТОП карамельный",
                        "quantity": 1,
                        "weight": 0.45,
                        "price": 2.55
                    },
                    {
                        "name": NULL,
                        "quantity": 1,
                        "weight": 0,
                        "price": NULL
                    }
                ],
                "totalPrice": 3.08
            }
        ',
    ],
    'default_structure' => [
        "organization" => NULL,
        "address" => [
            "city" => NULL,
            "street" => NULL,
            "entrance" => NULL
        ],
        "items" => [
            [
                "name" => NULL,
                "quantity" => NULL,
                "weight" => NULL,
                "price" => NULL
            ]
        ],
        "totalPrice" => NULL
    ]
];

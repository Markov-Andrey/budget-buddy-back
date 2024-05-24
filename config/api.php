<?php

return [
    'check_processing' => [
        'prompt' => '
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
                        "name": null,
                        "quantity": 1,
                        "weight": 0,
                        "price": null
                    }
                ]
            }
        ',
        'default_structure' => [
            "organization" => null,
            "address" => [
                "city" => null,
                "street" => null,
                "entrance" => null
            ],
            "items" => [
                [
                    "name" => null,
                    "quantity" => null,
                    "weight" => null,
                    "price" => null
                ]
            ],
        ]
    ],

    'check_subcategories' => [
        'prompt' => '
            Твоя роль - анализ данных и ответ СТРОГО в формате JSON.
            Я передаю тебе массив с категорией Продукты и всеми подкатегориями.
            Ты должен проанализировать список. И постараться найти соответствия между названиями товаров и подкатегориями.
            Постарайся заполнить все соответсвия, там где их нет - оставь null.
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
                        "name": null,
                        "quantity": 1,
                        "weight": 0,
                        "price": null
                    }
                ]
            }
        ',
        'default_structure' => [

        ]
    ],
];

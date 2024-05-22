<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\ReceiptsData;

use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<ReceiptsData>
 */
class ReceiptsDataResource extends ModelResource
{
    protected string $model = ReceiptsData::class;

    protected string $title = 'ReceiptsData';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('name', 'name'),
            Text::make('quantity', 'quantity'),
            Text::make('weight', 'weight'),
            Text::make('price', 'price'),
        ];
    }

    /**
     * @param ReceiptsData $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

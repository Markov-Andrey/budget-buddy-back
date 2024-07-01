<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Auto;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReceiptsData;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\MorphTo;
use MoonShine\Fields\Text;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends ModelResource<ReceiptsData>
 */
class ReceiptsDataResource extends ModelResource
{
    protected string $model = ReceiptsData::class;

    protected string $title = 'ReceiptsData';

    protected string $column = 'name';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('receipt' , 'receipt', resource: new ReceiptsResource()),
            Text::make('name', 'name'),
            Text::make('quantity', 'quantity'),
            Text::make('weight', 'weight'),
            Text::make('price', 'price'),
            BelongsTo::make('subcategory', 'subcategory', resource: new SubcategoriesResource())
                ->nullable()
                ->searchable(),
            MorphTo::make('morph')->types([
                Auto::class => 'name',
            ])
                ->nullable()
                ->searchable(),
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

    public function queryTags(): array
    {
        return [
            QueryTag::make(
                'Все',
                fn(Builder $query) => $query // Query builder
            ),
            QueryTag::make(
                'Пустые категории',
                fn(Builder $query) => $query->whereNull('subcategory_id') // Query builder
            )
        ];
    }
}

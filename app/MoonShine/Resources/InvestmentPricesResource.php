<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvestmentPrices;

use MoonShine\Fields\Date;
use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<InvestmentPrices>
 */
class InvestmentPricesResource extends ModelResource
{
    protected string $model = InvestmentPrices::class;

    protected string $title = 'InvestmentPrices';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                BelongsTo::make('investment_type_id', 'investmentType', resource: new InvestmentTypeResource()),
                Number::make('price', 'price'),
                Date::make('date', 'date')
                    ->format('d.m.Y'),
            ]),
        ];
    }

    /**
     * @param InvestmentPrices $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

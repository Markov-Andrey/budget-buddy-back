<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvestmentDetails;

use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<InvestmentDetails>
 */
class InvestmentDetailsResource extends ModelResource
{
    protected string $model = InvestmentDetails::class;

    protected string $title = 'InvestmentDetails';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                BelongsTo::make('investment', 'investment', resource: new InvestmentResource()),
                BelongsTo::make('investmentType', 'investmentType', resource: new InvestmentTypeResource()),
                Number::make('size', 'size', function ($item) {
                    return rtrim(rtrim(number_format((float)$item->size, 10, '.', ''), '0'), '.');
                })->step(0.0000000001),
                Number::make('cost_per_unit', 'cost_per_unit', function ($item) {
                    return rtrim(rtrim(number_format((float)$item->cost_per_unit, 10, '.', ''), '0'), '.');
                })->step(0.0000000001),
            ]),
        ];
    }

    /**
     * @param InvestmentDetails $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

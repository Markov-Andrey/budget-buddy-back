<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Investment;

use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<Investment>
 */
class InvestmentResource extends ModelResource
{
    protected string $model = Investment::class;

    protected string $title = 'Investments';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                BelongsTo::make('user', 'user', resource: new UserResource()),
                Number::make('total_amount', 'total_amount')->step(0.01),
                HasMany::make('investmentDetail', 'investmentDetail', resource: new InvestmentDetailsResource())
                    ->fields([
                        BelongsTo::make('investmentType', 'investmentType', resource: new InvestmentTypeResource()),
                        Number::make('size', 'size', function ($item) {
                            return rtrim(rtrim(number_format((float)$item->size, 10, '.', ''), '0'), '.');
                        })->step(0.0000000001),
                        Number::make('cost_per_unit', 'cost_per_unit', function ($item) {
                            return rtrim(rtrim(number_format((float)$item->cost_per_unit, 10, '.', ''), '0'), '.');
                        })->step(0.0000000001),
                    ])
            ]),
        ];
    }

    /**
     * @param Investment $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

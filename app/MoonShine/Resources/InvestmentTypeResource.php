<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvestmentType;

use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<InvestmentType>
 */
class InvestmentTypeResource extends ModelResource
{
    protected string $model = InvestmentType::class;

    protected string $title = 'InvestmentTypes';
    protected string $column = 'name';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                Text::make('name', 'name'),
                Text::make('code', 'code'),
                Text::make('coingecko_id', 'coingecko_id'),
                Text::make('nbrb_id', 'nbrb_id'),
            ]),
        ];
    }

    /**
     * @param InvestmentType $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

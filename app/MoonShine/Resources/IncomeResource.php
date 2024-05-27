<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use App\Models\Income;

use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Select;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<Income>
 */
class IncomeResource extends ModelResource
{
    protected string $model = Income::class;

    protected string $title = 'Incomes';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        $category = Category::where('name', 'Доход')->first();
        $items = [];
        if ($category) {
            $items = $category->subcategories->pluck('name', 'id')->toArray();
        }

        return [
            ID::make()->sortable(),
            BelongsTo::make('user', 'user', resource: new UserResource())
                ->nullable()
                ->searchable(),
            Select::make('subcategory', 'subcategory_id')
                ->options($items),
            Number::make('amount', 'amount')->step(0.01),
        ];
    }

    /**
     * @param Income $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

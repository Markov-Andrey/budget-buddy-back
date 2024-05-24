<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Subcategory;

use MoonShine\Fields\Date;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<Subcategory>
 */
class SubcategoriesResource extends ModelResource
{
    protected string $model = Subcategory::class;

    protected string $title = 'Subcategories';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('name', 'name'),
            BelongsTo::make('category_id', 'category_id', resource: new CategoriesResource()),
            Date::make('created_at', 'created_at')
                ->hideOnForm()
                ->format('d.m.y H:i'),
        ];
    }

    /**
     * @param Subcategory $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

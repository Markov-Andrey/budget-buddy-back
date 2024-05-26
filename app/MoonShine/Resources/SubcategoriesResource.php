<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Subcategory;

use MoonShine\Fields\Date;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
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

    protected string $column = 'name';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('name', 'name'),
            BelongsTo::make('category_id', 'category', resource: new CategoriesResource())
                ->searchable()
                ->nullable(),
            Switcher::make('is_check', 'is_check')
                ->updateOnPreview(),
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

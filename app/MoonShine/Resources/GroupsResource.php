<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Groups;

use MoonShine\Fields\Date;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<Groups>
 */
class GroupsResource extends ModelResource
{
    protected string $model = Groups::class;

    protected string $title = 'Groups';

    public string $column = 'title';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('title', 'title'),
            BelongsTo::make('admin', 'admin', resource: new UserResource())
                ->searchable()
                ->nullable(),
            Date::make('created_at', 'created_at')->hideOnForm(),
            Date::make('updated_at', 'updated_at')->hideOnForm(),
        ];
    }

    /**
     * @param Groups $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\GroupMemberships;

use MoonShine\Fields\Date;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<GroupMemberships>
 */
class GroupMembershipsResource extends ModelResource
{
    protected string $model = GroupMemberships::class;

    protected string $title = 'GroupMemberships';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('user', 'user', resource: new UserResource())
                ->nullable()
                ->searchable(),
            BelongsTo::make('group', 'group', resource: new GroupsResource())
                ->nullable()
                ->searchable(),
            Date::make('created_at', 'created_at')->hideOnForm(),
            Date::make('updated_at', 'updated_at')->hideOnForm(),
        ];
    }

    /**
     * @param GroupMemberships $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

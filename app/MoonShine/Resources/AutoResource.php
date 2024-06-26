<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\ReceiptsData;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auto;

use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\MorphToMany;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<Auto>
 */
class AutoResource extends ModelResource
{
    protected string $model = Auto::class;

    protected string $title = 'Auto';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('name', 'name'),
            BelongsTo::make('user', 'user', resource: new UserResource())
                ->nullable()
                ->searchable(),
            Number::make('service_interval', 'service_interval'),
        ];
    }

    /**
     * @param Auto $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

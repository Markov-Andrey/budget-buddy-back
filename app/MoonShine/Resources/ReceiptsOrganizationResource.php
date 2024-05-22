<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\ReceiptsOrganization;

use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<ReceiptsOrganization>
 */
class ReceiptsOrganizationResource extends ModelResource
{
    protected string $model = ReceiptsOrganization::class;

    protected string $title = 'ReceiptsOrganizations';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('name', 'name'),
            Text::make('city', 'city'),
            Text::make('street', 'street'),
            Text::make('entrance', 'entrance'),
        ];
    }

    /**
     * @param ReceiptsOrganization $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

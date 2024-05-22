<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Receipts;

use Illuminate\Support\Facades\App;
use MoonShine\Fields\Date;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<Receipts>
 */
class ReceiptsResource extends ModelResource
{
    protected string $model = Receipts::class;
    protected string $title = 'Чеки в обработке';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('image_path', 'image_path'),
            BelongsTo::make('user_id', 'user', resource: new UserResource()),
            Switcher::make('processed', 'processed'),
            Switcher::make('error', 'error'),
            Date::make('', 'created_at')->hideOnAll()->showOnIndex(),
        ];
    }

    /**
     * @param Receipts $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

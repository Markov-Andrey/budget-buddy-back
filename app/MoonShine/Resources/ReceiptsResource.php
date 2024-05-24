<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Jobs\ProcessReceipt;
use Illuminate\Database\Eloquent\Model;
use App\Models\Receipts;

use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Date;
use MoonShine\Fields\Image;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\HasMany;
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
     * @throws FieldException
     */
    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Image::make('image_path', 'image_path')
                ->dir('receipts'),
            BelongsTo::make('user_id', 'user', resource: new UserResource()),
            Switcher::make('processed', 'processed')
                ->hideOnCreate(),
            Switcher::make('error', 'error')
                ->hideOnCreate(),
            Switcher::make('annulled', 'annulled')
                ->hideOnCreate()
                ->updateOnPreview(),
            Date::make('created_at', 'created_at')
                ->format('d.m.y H:i')
                ->hideOnAll()
                ->showOnIndex()
                ->showOnDetail(),
            HasMany::make('address', 'address', resource: new ReceiptsOrganizationResource)
                ->fields([
                    Text::make('name', 'name'),
                    Text::make('city', 'city'),
                    Text::make('street', 'street'),
                    Text::make('entrance', 'entrance'),
                ])
                ->hideOnAll()
                ->showOnDetail(),
            HasMany::make('data', 'data', resource: new ReceiptsDataResource)
                ->fields([
                    Text::make('name', 'name'),
                    Text::make('quantity', 'quantity'),
                    Text::make('weight', 'weight'),
                    Text::make('price', 'price'),
                ])
                ->hideOnAll()
                ->showOnDetail(),
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

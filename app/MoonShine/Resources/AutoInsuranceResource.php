<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\ReceiptsData;
use Illuminate\Database\Eloquent\Model;
use App\Models\AutoInsurance;

use MoonShine\Fields\Date;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<AutoInsurance>
 */
class AutoInsuranceResource extends ModelResource
{
    protected string $model = AutoInsurance::class;

    protected string $title = 'Insurances/Страховка';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('receiptsData', 'receiptsData', fn($item) => ReceiptsData::formattedData($item), resource: new ReceiptsDataResource())
                ->nullable()
                ->searchable(),
            Date::make('expiry_date', 'expiry_date'),
        ];
    }

    /**
     * @param AutoInsurance $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\ReceiptsData;
use Illuminate\Database\Eloquent\Model;
use App\Models\AutoTechnicalInspection;

use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<AutoTechnicalInspection>
 */
class AutoTechnicalInspectionResource extends ModelResource
{
    protected string $model = AutoTechnicalInspection::class;

    protected string $title = 'Technical Inspections/Плановое ТО';

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
            Number::make('inspection_mileage', 'inspection_mileage'),
        ];
    }

    /**
     * @param AutoTechnicalInspection $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}

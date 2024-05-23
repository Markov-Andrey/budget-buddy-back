<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Jobs;

use MoonShine\Fields\Date;
use MoonShine\Fields\Json;
use MoonShine\Fields\Number;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<Jobs>
 */
class JobsResource extends ModelResource
{
    protected string $model = Jobs::class;

    protected string $title = 'Jobs';

    /**
     * @return list<MoonShineComponent|Field>
     * @throws \Throwable
     */
    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('queue', 'queue'),
            Text::make('payload', 'payload', fn($item) => $this->formatArray($item->payload)),
            Date::make('available_at','available_at')->format('d.m.y H:i'),
            Date::make('created_at','created_at')->format('d.m.y H:i'),
        ];
    }

    /**
     * @param Jobs $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }

    function formatArray($array) {
        $result = '';
        $fields = ['uuid', 'displayName', 'job'];

        foreach ($fields as $field) {
            if (isset($array[$field])) {
                $result .= "<b>$field</b>: " . $array[$field] . "<br>";
            }
        }

        return $result;
    }

    public function getActiveActions(): array
    {
        return [
            // 'view',
            // 'create',
            // 'update',
            'delete',
            'massDelete',
        ];
    }
}

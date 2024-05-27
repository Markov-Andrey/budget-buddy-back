<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\MoonShine\Components\Info\InfoBlock;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Pages\Page;

class InfoPage extends Page
{
    /**
     * @return array<string, string>
     */
    public function breadcrumbs(): array
    {
        return [
            '#' => $this->title()
        ];
    }

    public function title(): string
    {
        return $this->title ?: 'InfoPage';
    }

    /**
     * @return list<MoonShineComponent>
     */
    public function components(): array
	{
		return [
            InfoBlock::make(),
        ];
	}
}

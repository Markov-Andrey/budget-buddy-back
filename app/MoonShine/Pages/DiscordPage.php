<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\MoonShine\Components\DiscordInfoComponent;
use MoonShine\Pages\Page;
use MoonShine\Components\MoonShineComponent;

class DiscordPage extends Page
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
        return $this->title ?: 'Discord Bot Info';
    }

    /**
     * @return list<MoonShineComponent>
     */
    public function components(): array
	{
		return [
            DiscordInfoComponent::make(),
        ];
	}
}

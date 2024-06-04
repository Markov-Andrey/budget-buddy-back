<?php

declare(strict_types=1);

namespace App\MoonShine\Components;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Components\MoonShineComponent;

/**
 * @method static static make()
 */
final class DiscordInfoComponent extends MoonShineComponent
{
    protected string $view = 'admin.components.discord-info-component';

    public function __construct()
    {
        //
    }

    /*
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [];
    }
}

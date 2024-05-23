<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Jobs;
use App\Models\Receipts;
use App\MoonShine\Resources\GroupMembershipsResource;
use App\MoonShine\Resources\GroupsResource;
use App\MoonShine\Resources\JobsResource;
use App\MoonShine\Resources\ReceiptsDataResource;
use App\MoonShine\Resources\ReceiptsOrganizationResource;
use App\MoonShine\Resources\ReceiptsResource;
use App\MoonShine\Resources\UserResource;
use App\Observers\ReceiptObserver;
use MoonShine\Providers\MoonShineApplicationServiceProvider;
use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use MoonShine\Resources\MoonShineUserResource;
use MoonShine\Resources\MoonShineUserRoleResource;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Menu\MenuElement;
use MoonShine\Pages\Page;
use Closure;

class MoonShineServiceProvider extends MoonShineApplicationServiceProvider
{
    /**
     * @return list<ResourceContract>
     */
    protected function resources(): array
    {
        return [];
    }

    /**
     * @return list<Page>
     */
    protected function pages(): array
    {
        return [];
    }

    /**
     * @return Closure|list<MenuElement>
     */
    protected function menu(): array
    {
        return [
            MenuGroup::make(static fn() => __('moonshine::ui.resource.system'), [
               MenuItem::make(
                   static fn() => __('moonshine::ui.resource.admins_title'),
                   new MoonShineUserResource()
               ),
               MenuItem::make(
                   static fn() => __('moonshine::ui.resource.role_title'),
                   new MoonShineUserRoleResource()
               ),
                MenuItem::make(
                    static fn() => __('Users'),
                    new UserResource()
                ),
            ]),
            MenuItem::make(
                'Чеки в обработке',
                new ReceiptsResource()
            ),
            MenuItem::make(
                'Товары из чеков',
                new ReceiptsDataResource()
            ),
            MenuItem::make(
                'Адреса',
                new ReceiptsOrganizationResource()
            ),
            MenuItem::make(
                'Worker',
                new JobsResource()
            )->badge(fn() => Jobs::query()->count()),
            MenuItem::make(
                'Group',
                new GroupsResource()
            ),
            MenuItem::make(
                'GroupMemberships',
                new GroupMembershipsResource()
            ),
        ];
    }
    public function boot(): void
    {
        parent::boot();
        Receipts::observe(ReceiptObserver::class);
    }

    /**
     * @return Closure|array{css: string, colors: array, darkColors: array}
     */
    protected function theme(): array
    {
        return [];
    }
}

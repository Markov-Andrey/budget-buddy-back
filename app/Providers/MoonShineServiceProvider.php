<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Jobs;
use App\Models\Receipts;
use App\Models\ReceiptsData;
use App\MoonShine\Pages\DiscordPage;
use App\MoonShine\Pages\InfoPage;
use App\MoonShine\Resources\AutoInsuranceResource;
use App\MoonShine\Resources\AutoResource;
use App\MoonShine\Resources\AutoTechnicalInspectionResource;
use App\MoonShine\Resources\CategoriesResource;
use App\MoonShine\Resources\DiscordMessageResource;
use App\MoonShine\Resources\GroupMembershipsResource;
use App\MoonShine\Resources\GroupsResource;
use App\MoonShine\Resources\IncomeResource;
use App\MoonShine\Resources\InvestmentDetailsResource;
use App\MoonShine\Resources\InvestmentPricesResource;
use App\MoonShine\Resources\InvestmentResource;
use App\MoonShine\Resources\InvestmentTypeResource;
use App\MoonShine\Resources\JobsResource;
use App\MoonShine\Resources\ReceiptsDataResource;
use App\MoonShine\Resources\ReceiptsOrganizationResource;
use App\MoonShine\Resources\ReceiptsResource;
use App\MoonShine\Resources\SubcategoriesResource;
use App\MoonShine\Resources\UserResource;
use App\Observers\ReceiptDataObserver;
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
            ]),
            MenuGroup::make('Discord', [
                MenuItem::make(
                    'Discord Bot Info',
                    new DiscordPage()
                ),
                MenuItem::make(
                    'Messages',
                    new DiscordMessageResource()
                ),
            ]),
            MenuGroup::make('Чеки', [
                MenuItem::make(
                    'Worker',
                    new JobsResource()
                )->badge(fn() => Jobs::query()->count()),
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
            ]),
            MenuGroup::make('Пользователи/группы', [
                MenuItem::make(
                    static fn() => __('Users'),
                    new UserResource()
                ),
                MenuItem::make(
                    'Group',
                    new GroupsResource()
                ),
                MenuItem::make(
                    'GroupMemberships',
                    new GroupMembershipsResource()
                ),
            ]),
            MenuGroup::make('Категории/подкатегории', [
                MenuItem::make(
                    'Categories',
                    new CategoriesResource()
                ),
                MenuItem::make(
                    'Subcategories',
                    new SubcategoriesResource()
                ),
            ]),
            MenuGroup::make('Auto', [
                MenuItem::make(
                    'Auto List',
                    new AutoResource()
                ),
                MenuItem::make(
                    'Insurance',
                    new AutoInsuranceResource()
                ),
                MenuItem::make(
                    'Technical Inspection',
                    new AutoTechnicalInspectionResource()
                ),
            ]),
            MenuGroup::make('Investment', [
                MenuItem::make(
                    'Types',
                    new InvestmentTypeResource()
                ),
                MenuItem::make(
                    'Investment',
                    new InvestmentResource()
                ),
                MenuItem::make(
                    'Investment Detail',
                    new InvestmentDetailsResource()
                ),
                MenuItem::make(
                    'Investment Prices',
                    new InvestmentPricesResource()
                ),
            ]),
            MenuItem::make(
                'Income',
                new IncomeResource()
            ),
            MenuItem::make(
                'InfoPage',
                new InfoPage()
            ),
        ];
    }
    public function boot(): void
    {
        parent::boot();
        Receipts::observe(ReceiptObserver::class);
        ReceiptsData::observe(ReceiptDataObserver::class);
    }

    /**
     * @return Closure|array{css: string, colors: array, darkColors: array}
     */
    protected function theme(): array
    {
        return [
            'colors' => [
                'primary' => '#08D49E',
                'secondary' => '#EFBA0C',
            ],
        ];
    }
}

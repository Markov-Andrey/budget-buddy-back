<?php

declare(strict_types=1);

namespace App\MoonShine\Components\Info;

use App\Models\Auto;
use App\Models\GroupMemberships;
use App\Models\Groups;
use App\Models\Income;
use App\Models\InvestmentDetails;
use App\Models\Receipts;
use App\Models\User;
use MoonShine\Components\MoonShineComponent;

/**
 * @method static static make()
 */
final class InfoBlock extends MoonShineComponent
{
    protected string $view = 'admin.components.info-block';
    protected mixed $user;
    protected mixed $users;
    protected mixed $group;
    protected mixed $groups;
    protected mixed $data;
    protected mixed $categoriesData;
    private array $subCategoriesDataProducts;
    private array $subCategoriesDataAuto;
    private array $subCategoriesDataPermanent;
    private mixed $amountData;
    private mixed $autoData;
    private mixed $incomeAverage;
    private mixed $lossAverage;
    private mixed $investmentData;
    private mixed $sumInvestmentData = 0;
    private mixed $sumInvestmentCurrentData = 0;

    public function __construct()
    {
        $id = request('user');
        $groupId = request('group');

        // Извлекаем пользователя, если передан user_id
        $this->user = $id ? User::query()->findOrFail($id) : null;
        $this->users = User::all();

        // Извлекаем группу, если передан group_id
        $this->group = $groupId ? Groups::query()->findOrFail($groupId) : null;
        $this->groups = Groups::with(['groupMemberships.user'])->get();

        // Извлекаем ids пользователей, входящих в группу
        if ($this->group) {
            $userIds = GroupMemberships::query()
                ->where('group_id', '=', $groupId)
                ->pluck('user_id') // Извлекаем user_id, а не id
                ->toArray();
        } else {
            $userIds = [$id];
        }

        // Добавление admin_id к пользователям группы
        foreach ($this->groups as $group) {
            foreach ($group->groupMemberships as $membership) {
                $membership->user->admin_id = $group->admin_id;
            }
        }

        $this->subCategoriesDataProducts = Receipts::calculatePricesBySubcategory($userIds, 'Продукты');
        $this->subCategoriesDataAuto = Receipts::calculatePricesBySubcategory($userIds, 'Автомобиль');
        $this->subCategoriesDataPermanent = Receipts::calculatePricesBySubcategory($userIds, 'Постоянные');
        $this->categoriesData = Receipts::calculatePricesByCategory($userIds);
        $this->amountData = Income::calculateByCategory($userIds);

        $this->incomeAverage = Income::averageMonthlyLastYear($userIds);
        $this->lossAverage = Receipts::averageMonthlyLastYear($userIds);

        $this->autoData = Auto::getAutoDataByUserId($userIds);

        $this->investmentData = InvestmentDetails::getInvestmentDetailsData($userIds);
        $this->sumInvestmentData = 0; // Инициализация переменной
        $this->sumInvestmentCurrentData = 0; // Инициализация переменной
        foreach ($this->investmentData as $data) {
            $this->sumInvestmentData += $data['total_value'];
            $this->sumInvestmentCurrentData += $data['latest_amount'];
        }
    }

    /*
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'user' => $this->user,
            'users' => $this->users,
            'group' => $this->group,
            'groups' => $this->groups,
            'categoriesData' => $this->categoriesData,
            'subCategoriesData' => $this->subCategoriesDataProducts,
            'subCategoriesDataAuto' => $this->subCategoriesDataAuto,
            'subCategoriesDataPermanent' => $this->subCategoriesDataPermanent,
            'amountData' => $this->amountData,
            'autoData' => $this->autoData,
            'incomeAverage' => $this->incomeAverage,
            'lossAverage' => $this->lossAverage,
            'investmentData' => $this->investmentData,
            'sumInvestmentData' => $this->sumInvestmentData,
            'sumInvestmentCurrentData' => $this->sumInvestmentCurrentData,
        ];
    }
}

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
        $this->user = $id ? User::query()->findOrFail($id) : null;
        $this->users = User::all();
        $this->group = $groupId ? Groups::query()->findOrFail($groupId) : null;
        $this->groups = Groups::with('groupMemberships', 'groupMemberships.user')->get();
        if ($this->group) {
            $id = GroupMemberships::query()->where('group_id', '=', $groupId)->pluck('id')->toArray();
        }

        $this->subCategoriesDataProducts = Receipts::calculatePricesBySubcategory($id, 'Продукты');
        $this->subCategoriesDataAuto = Receipts::calculatePricesBySubcategory($id, 'Автомобиль');
        $this->subCategoriesDataPermanent = Receipts::calculatePricesBySubcategory($id, 'Постоянные');
        $this->categoriesData = Receipts::calculatePricesByCategory($id);
        $this->amountData = Income::calculateByCategory($id);
        $this->amountData = Income::calculateByCategory($id);

        $this->incomeAverage = Income::averageMonthlyLastYear($id);
        $this->lossAverage = Receipts::averageMonthlyLastYear($id);

        $this->autoData = Auto::getAutoDataByUserId($id);

        $this->investmentData = InvestmentDetails::getInvestmentDetailsData($id);
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

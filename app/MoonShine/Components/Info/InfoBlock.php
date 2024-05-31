<?php

declare(strict_types=1);

namespace App\MoonShine\Components\Info;

use App\Models\Auto;
use App\Models\Income;
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
    protected mixed $data;
    protected mixed $categoriesData;
    private array $subCategoriesData;
    private array $subCategoriesDataAuto;
    private mixed $amountData;

    private mixed $income;
    private mixed $loss;
    private mixed $balance;
    private mixed $autoData;
    private mixed $averageIncome;

    public function __construct()
    {
        $id = request('user');
        $this->user = $id ? User::query()->findOrFail($id) : null;
        $this->users = User::all();

        $this->subCategoriesData = Receipts::calculatePricesBySubcategory($id, 'Продукты');
        $this->subCategoriesDataAuto = Receipts::calculatePricesBySubcategory($id, 'Автомобиль');
        $this->categoriesData = Receipts::calculatePricesByCategory($id);
        $this->amountData = Income::calculateByCategory($id);
        $this->amountData = Income::calculateByCategory($id);

        $this->income = number_format(Income::totalIncomeUser($id), 2);
        $this->loss = number_format(Receipts::totalLossUser($id), 2);
        $this->balance = number_format($this->income - $this->loss, 2);

        $this->autoData = Auto::getAutoDataByUserId($id);
        $this->averageIncome = Income::averageMonthlyIncomeLastYear($id);
    }

    /*
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'users' => $this->users,
            'user' => $this->user,
            'categoriesData' => $this->categoriesData,
            'subCategoriesData' => $this->subCategoriesData,
            'subCategoriesDataAuto' => $this->subCategoriesDataAuto,
            'amountData' => $this->amountData,
            'income' => $this->income,
            'loss' => $this->loss,
            'balance' => $this->balance,
            'autoData' => $this->autoData,
            'averageIncome' => $this->averageIncome,
        ];
    }
}

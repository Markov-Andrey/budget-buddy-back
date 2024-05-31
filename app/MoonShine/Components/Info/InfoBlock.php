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
    private mixed $autoData;
    private mixed $incomeAverage;
    private mixed $lossAverage;

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

        $this->incomeAverage = Income::averageMonthlyLastYear($id);
        $this->lossAverage = Receipts::averageMonthlyLastYear($id);

        $this->autoData = Auto::getAutoDataByUserId($id);
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
            'autoData' => $this->autoData,
            'incomeAverage' => $this->incomeAverage,
            'lossAverage' => $this->lossAverage,
        ];
    }
}

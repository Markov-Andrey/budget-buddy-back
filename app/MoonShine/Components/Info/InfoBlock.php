<?php

declare(strict_types=1);

namespace App\MoonShine\Components\Info;

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
    private mixed $amountData;

    private mixed $income;
    private mixed $loss;
    private mixed $balance;

    public function __construct()
    {
        $id = request('user');
        $this->user = $id ? User::query()->findOrFail($id) : null;
        $this->users = User::all();

        $this->subCategoriesData = Receipts::calculatePricesByCategory($id, 'Продукты');
        $this->categoriesData = Receipts::calculatePricesByCategory($id);
        $this->amountData = Income::calculateByCategory($id);

        $this->income = number_format(Income::totalIncomeUser($id), 2);
        $this->loss = number_format(Receipts::totalLossUser($id), 2);
        $this->balance = number_format($this->income - $this->loss, 2);
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
            'amountData' => $this->amountData,
            'income' => $this->income,
            'loss' => $this->loss,
            'balance' => $this->balance,
        ];
    }
}
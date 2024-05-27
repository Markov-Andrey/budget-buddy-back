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

    public function __construct()
    {
        $id = request('user');
        $this->user = $id ? User::query()->findOrFail($id) : null;

        $this->subCategoriesData = Receipts::calculatePricesByCategory($id, 'Продукты');
        $this->categoriesData = Receipts::calculatePricesByCategory($id);
        $this->users = User::all();

        $this->amountData = Income::calculateByCategory($id);
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
        ];
    }
}

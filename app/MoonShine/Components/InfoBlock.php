<?php

declare(strict_types=1);

namespace App\MoonShine\Components;

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
    protected mixed $dataTotal = 0;

    public function __construct()
    {
        $id = request('user');
        $this->user = $id ? User::query()->findOrFail($id) : null;

        $this->data = Receipts::calculatePricesByCategory($id, 'Продукты');
        $this->categoriesData = Receipts::calculatePricesByCategory($id);
        $this->users = User::all();
    }

    /*
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'users' => $this->users,
            'user' => $this->user,
            'data' => $this->data['details'],
            'dataTotal' => $this->data['dataTotal'],
            'categoriesData' => $this->categoriesData['details'],
        ];
    }
}

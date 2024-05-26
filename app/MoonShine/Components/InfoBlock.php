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

        $receipts = $id ? Receipts::with('data', 'data.subcategory', 'data.subcategory.category')
            ->where('user_id', $id)
            ->first()
            ->data
            ->groupBy('subcategory_id')
            : null;

        $detailsProducts = [];
        $detailsCategory = [];

        if ($receipts) {
            foreach ($receipts as $items) {
                $totalPrice = 0;
                foreach ($items as $item) {
                    $price = str_replace(',', '.', $item->price);
                    $quantity = $item->quantity;
                    if (is_numeric($price) && is_numeric($quantity)) {
                        $totalPrice += $price * $quantity;
                    }
                }
                $subcategoryName = $items->first()->subcategory->name;
                $categoryName = $items->first()->subcategory->category->name;

                $detailsProducts[$subcategoryName] = $totalPrice;
                $this->dataTotal += $totalPrice;

                if (!isset($detailsCategory[$categoryName])) {
                    $detailsCategory[$categoryName] = 0;
                }
                $detailsCategory[$categoryName] += $totalPrice;
            }
        }

        arsort($detailsProducts);
        arsort($detailsCategory);

        $this->data = $detailsProducts;
        $this->categoriesData = $detailsCategory;
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
            'data' => $this->data,
            'dataTotal' => $this->dataTotal,
            'categoriesData' => $this->categoriesData,
        ];
    }
}

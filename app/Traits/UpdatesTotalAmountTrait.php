<?php

namespace App\Traits;

use App\Models\Receipts;

trait UpdatesTotalAmountTrait
{
    /**
     * Обновление общей суммы amount для записи Receipts по связанным данным ReceiptsData.
     *
     * @param  \App\Models\Receipts  $receipt
     * @return void
     */
    protected function updateTotalAmount(Receipts $receipt)
    {
        $totalAmount = 0;
        $receiptsData = $receipt->data;
        foreach ($receiptsData as $data) {
            $price = $data->price ? str_replace(',', '.', $data->price) : 0;
            $quantity = $data->quantity !== 0 ? $data->quantity : 1;
            $totalAmount += $price * $quantity;
        }
        if ($receipt->amount !== $totalAmount) {
            $receipt->amount = $totalAmount;
            $receipt->save();
        }
    }
}

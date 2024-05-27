<?php

namespace App\Observers;

use App\Models\ReceiptsData;
use App\Traits\UpdatesTotalAmountTrait;

class ReceiptDataObserver
{
    use UpdatesTotalAmountTrait;

    public function created(ReceiptsData $receiptsData)
    {
        $this->updateTotalAmount($receiptsData->receipt);
    }

    public function updated(ReceiptsData $receiptsData)
    {
        $this->updateTotalAmount($receiptsData->receipt);
    }
}

<?php

namespace App\Observers;

use App\Jobs\CategorizeProductsJobs;
use App\Jobs\ProcessReceiptJobs;
use App\Models\Receipts;

class ReceiptObserver
{
    /**
     * Handle the Receipt "created" event.
     */
    public function created(Receipts $receipt): void
    {
        ProcessReceiptJobs::dispatch($receipt);
        CategorizeProductsJobs::dispatch($receipt);
    }

    /**
     * Handle the Receipt "updated" event.
     */
    public function updated(Receipts $receipt): void
    {
        //
    }

    /**
     * Handle the Receipt "deleted" event.
     */
    public function deleted(Receipts $receipt): void
    {
        //
    }

    /**
     * Handle the Receipt "restored" event.
     */
    public function restored(Receipts $receipt): void
    {
        //
    }

    /**
     * Handle the Receipt "force deleted" event.
     */
    public function forceDeleted(Receipts $receipt): void
    {
        //
    }

    /**
     * Handle the Receipt "massInserted" event.
     *
     * This method is called manually in case of bulk data insertion.
     *
     * @param  array  $receipts  The array of receipts that were inserted.
     * @return void
     */
    // Массовая вставка данных
    // Receipts::insert($data);
    // Получите последние вставленные записи
    // $receipts = Receipts::latest()->take(count($data))->get()->all();
    // Вызов наблюдателя для массовой вставки
    // (new ReceiptObserver)->massInserted($receipts);
    public function massInserted(array $receipts)
    {
        foreach ($receipts as $receipt) {
            ProcessReceiptJobs::dispatch($receipt);
            CategorizeProductsJobs::dispatch($receipt);
        }
    }
}

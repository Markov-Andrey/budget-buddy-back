<?php

namespace App\Jobs;

use App\Models\Receipts;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessReceipt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $receipt;

    public function __construct(Receipts $receipt)
    {
        $this->receipt = $receipt;
    }

    public function handle()
    {
        // TODO Реальная логика обработки чека
        $this->receipt->processed = true;
        $this->receipt->save();
    }
}

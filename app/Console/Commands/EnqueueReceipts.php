<?php

namespace App\Console\Commands;

use App\Jobs\CategorizeProductsJobs;
use Illuminate\Console\Command;
use App\Models\Receipts;
use App\Jobs\ProcessReceiptJobs;
use Illuminate\Support\Facades\DB;

class EnqueueReceipts extends Command
{
    protected $signature = 'receipts:add';
    protected $description = 'Add unprocessed receipts to the queue';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $receipts = Receipts::where('processed', false)
            ->where('error', false)
            ->get();

        foreach ($receipts as $receipt) {
            $jobInQueue = DB::table('jobs')->where('payload', 'like', '%"receipt_id":' . $receipt->id . '%')->exists();

            if (!$jobInQueue) {
                ProcessReceiptJobs::dispatch($receipt);
                CategorizeProductsJobs::dispatch($receipt);
                $this->info('Receipt ID ' . $receipt->id . ' added to the queue.');
            } else {
                $this->info('Receipt ID ' . $receipt->id . ' is already in the queue.');
            }
        }

        $this->info('All unprocessed receipts have been enqueued.');
    }
}

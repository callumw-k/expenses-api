<?php

namespace App\Jobs;

use App\Models\Expense;
use App\Services\ImageParsingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AttachImageToExpenseJob implements ShouldQueue
{
    use Queueable, Dispatchable;


    protected string $expenseId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $expenseId)
    {
        $this->expenseId = $expenseId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug("Processing OCR");
        $expense = Expense::find($this->expenseId);

        if (!$expense || $expense->image_path === null) {
            return;
        }

        $image_path = $expense->image_path;


        $receipt = ImageParsingService::ReceiptFromImage($image_path);

        if (!$receipt) return;

        $expense->total_amount = $receipt->getTotalAmount();
        $expense->tax_amount = $receipt->getTaxAmount();
        $expense->merchant_name = $receipt->getMerchantName();

        $expense->save();

    }

}



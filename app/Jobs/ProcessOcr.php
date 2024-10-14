<?php

namespace App\Jobs;

use App\Models\Expense;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class ProcessOcr implements ShouldQueue
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
        Log::info("Processing OCR");
        $expense = Expense::find($this->expenseId);
        if ($expense === null || $expense->file_path === null) {
            return;
        }
        $image = $expense->file_path;
        try {
            $text = (new TesseractOCR($image))->run();
            Log::info($text);
        } catch (TesseractOcrException $e) {
            Log::error($e->getMessage());
        }
    }
}

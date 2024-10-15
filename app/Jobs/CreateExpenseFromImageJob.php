<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ImageParsingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class CreateExpenseFromImageJob implements ShouldQueue
{
    use Queueable, Dispatchable;

    protected string $imagePath;
    protected User $user;
    protected string $description;

    /**
     * Create a new job instance.
     */
    public function __construct(string $imagePath, User $user, string|null $description)
    {
        $this->imagePath = $imagePath;
        $this->user = $user;
        $this->description = $description ?? '';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $receipt = ImageParsingService::ReceiptFromImage($this->imagePath);
        $this->user->expenses()->create([
            'file_path' => $this->imagePath,
            'merchant_name' => $receipt->merchant_name,
            'total_amount' => $receipt->getTotalAmount(),
            'tax_amount' => $receipt->getTaxAmount(),
            'name' => $this->description ?? '',
        ]);
    }
}

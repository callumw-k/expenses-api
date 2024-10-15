<?php

namespace App\DTOs;

use Illuminate\Support\Facades\Log;

class ReceiptDataDto
{
    public ?float $total_amount;
    public ?string $merchant_name;
    public ?float $tax_amount;

    public function __construct(?float $total_amount, ?string $merchant_name, ?float $tax)
    {
        $this->total_amount = $total_amount;
        $this->merchant_name = $merchant_name;
        $this->tax_amount = $tax;
    }

    public static function fromJson(string $json): ?self
    {
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON decode error: ' . json_last_error_msg());
            return null;
        }

        return new self(
            $data['total_amount'] ?? null,
            $data['merchant_name'] ?? null,
            $data['tax_amount'] ?? null
        );
    }

    public function getTotalAmount(): ?int
    {
        return $this->total_amount !== null ? (int)(round($this->total_amount, 2) * 100) : null;
    }

    public function getMerchantName(): ?string
    {
        return $this->merchant_name;
    }

    public function getTaxAmount(): ?int
    {
        return $this->tax_amount !== null ? (int)(round($this->tax_amount, 2) * 100) : null;
    }
}

<?php

namespace App\Models;

use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    /** @use HasFactory<ExpenseFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'total_amount',
        'merchant_name',
        'file_path',
        'tax_amount',
        'description'

    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

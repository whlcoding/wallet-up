<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Transaction
 */
class Transaction extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'wallet_id',
        'category_id',
        'name',
        'description',
        'amount',
        'type',
        'due_date',
        'status',
        'is_active',
        'is_recurring',
        'recurring_transaction_id'
    ];

    // Relationships

    /**
     * wallet
     *
     * @return BelongsTo
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_id', 'id');
    }

    /**
     * category
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'category_id', 'id');
    }

    public static function availableTypes(): array
    {
        return [
            'expense',
            'income',
        ];
    }

    public static function availableStatuses(): array
    {
        return [
            'pending',
            'paid',
            'overdue',
            'voided'
        ];
    }
}

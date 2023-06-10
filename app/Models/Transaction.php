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

    /** Transaction Types */
    const TYPE_EXPENSE = 'expense';
    const TYPE_INCOME = 'income';


    /** Transaction Statuses */
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_VOIDED = 'voided';

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
            self::TYPE_EXPENSE,
            self::TYPE_INCOME,
        ];
    }

    public static function availableStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PAID,
            self::STATUS_OVERDUE,
            self::STATUS_VOIDED,
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RecurringTransaction
 */
class RecurringTransaction extends Model
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
        'frequency',
        'start_date',
        'end_date',
        'is_active'
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
}

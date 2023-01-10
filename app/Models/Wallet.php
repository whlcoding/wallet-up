<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;


    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    // Relationships

    /**
     * user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * recurringTransactions
     *
     * @return HasMany
     */
    public function recurringTransactions(): HasMany
    {
        return $this->hasMany(RecurringTransaction::class, 'wallet_id', 'id');
    }

    /**
     * transactions
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Wallet::class, 'wallet_id', 'id');
    }
}

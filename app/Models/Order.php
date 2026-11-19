<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const PAID_STATUSES = ['paid', 'partially_refunded', 'refunded'];

    // Statuses that mean the customer paid, including orders refunded later
    protected $fillable = ['user_id',
        'reference',
        'status',
        'total',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'paid_at',
        'shipped_at',];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    // Pending refunds count too, so two refunds can't add up to more than the total
    public function refundedTotal(): float
    {
        return (float) $this->refunds()->whereIn('status', ['pending', 'succeeded'])->sum('amount');
    }

    public function refundableAmount(): float
    {
        return round((float) $this->total - $this->refundedTotal(), 2);
    }

    public function canBeRefunded(): bool
    {
        return in_array($this->status, ['paid', 'partially_refunded'], true)
            && $this->stripe_payment_intent_id !== null
            && $this->refundableAmount() > 0;
    }

    public function getRefundedAmountAttribute(): float
    {
        return $this->refundedTotal();
    }
}

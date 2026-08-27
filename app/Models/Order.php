<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_code', 'order_type', 'customer_name', 'table_number',
    'customer_note', 'status', 'payment_method', 'amount_paid', 'change_amount',
    'total', 'cashier_id', 'session_id',
    'confirmed_at', 'paid_at', 'completed_at',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    /**
     * Items in this order.
     *
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Cashier who handled this order.
     *
     * @return BelongsTo<User, $this>
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Scope: only pending orders.
     *
     * @param  Builder<Order>  $query
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: orders created today.
     *
     * @param  Builder<Order>  $query
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Get a human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'paid' => 'Sudah Dibayar',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get formatted total in Rupiah.
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp '.number_format($this->total, 0, ',', '.');
    }

    /**
     * Get a human-readable order type label.
     */
    public function getOrderTypeLabelAttribute(): string
    {
        return $this->order_type === 'dine_in' ? 'Dine In' : 'Take Away';
    }

    /**
     * Whether this order is for dine-in.
     */
    public function isDineIn(): bool
    {
        return $this->order_type === 'dine_in';
    }

    /**
     * Generate a unique order code with daily resetting sequence (ORD-YYYYMMDD-0001).
     */
    public static function generateOrderCode(): string
    {
        $date = now()->format('Ymd');
        $lastOrderToday = static::whereDate('created_at', today())
            ->where('order_code', 'like', "ORD-{$date}-%")
            ->latest('id')
            ->first();

        if ($lastOrderToday && preg_match('/-(\d+)$/', $lastOrderToday->order_code, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        $sequence = str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);

        return "ORD-{$date}-{$sequence}";
    }

    /**
     * Get a human-readable payment method label.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Tunai (Cash)',
            'cashless' => 'Non-Tunai (QRIS/Debit)',
            default => '-',
        };
    }

    /**
     * Cast attributes.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'paid_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
}

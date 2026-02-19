<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_number',
        'customer_name',
        'order_type',
        'total_amount',
        'order_status',
        'payment_status',
        'created_by',
        'order_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'order_date' => 'datetime',
    ];

    /**
     * Get the user who created this order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the order items for this order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payments for this order.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Calculate total paid amount.
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->sum('amount_paid');
    }

    /**
     * Calculate remaining balance.
     */
    public function getRemainingBalanceAttribute(): float
    {
        return $this->total_amount - $this->getTotalPaidAttribute();
    }

    /**
     * Update payment status based on payments.
     */
    public function updatePaymentStatus(): void
    {
        $totalPaid = $this->getTotalPaidAttribute();

        if ($totalPaid == 0) {
            $this->payment_status = 'Unpaid';
        } elseif ($totalPaid >= $this->total_amount) {
            $this->payment_status = 'Paid';
        } else {
            $this->payment_status = 'Partial';
        }

        $this->save();
    }

    /**
     * Generate unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $lastOrder = self::whereDate('created_at', today())->latest()->first();
        $number = $lastOrder ? (int) substr($lastOrder->order_number, -4) + 1 : 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $number);
    }
}

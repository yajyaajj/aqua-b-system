<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventory';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_name',
        'category',
        'unit_price',
        'quantity_in_stock',
        'reorder_level',
        'supplier_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity_in_stock' => 'integer',
        'reorder_level' => 'integer',
    ];

    /**
     * Get the order items for this inventory.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if item is low in stock.
     */
    public function isLowStock(): bool
    {
        return $this->quantity_in_stock <= $this->reorder_level;
    }

    /**
     * Deduct stock quantity.
     */
    public function deductStock(int $quantity): bool
    {
        if ($this->quantity_in_stock >= $quantity) {
            $this->quantity_in_stock -= $quantity;
            return $this->save();
        }
        return false;
    }

    /**
     * Add stock quantity.
     */
    public function addStock(int $quantity): bool
    {
        $this->quantity_in_stock += $quantity;
        return $this->save();
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::where('email', 'admin@aquab.com')->first();
        $staff = \App\Models\User::where('email', 'staff@aquab.com')->first();

        // Create 5 sample orders
        for ($i = 1; $i <= 5; $i++) {
            // Add a small delay to ensure unique order numbers
            usleep(1000);
            
            $order = \App\Models\Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_name' => 'Customer ' . $i,
                'order_type' => ['Walk-in', 'Delivery', 'Refill'][rand(0, 2)],
                'total_amount' => 0, // Will be calculated
                'order_status' => $i <= 3 ? 'Completed' : 'Pending',
                'payment_status' => 'Unpaid',
                'created_by' => rand(0, 1) ? $admin->id : $staff->id,
                'order_date' => now()->subDays(rand(0, 7)),
            ]);

            // Add 2-3 random items to each order
            $numItems = rand(2, 3);
            $total = 0;

            for ($j = 0; $j < $numItems; $j++) {
                $inventory = \App\Models\Inventory::inRandomOrder()->first();
                $quantity = rand(1, 5);
                $price = $inventory->unit_price;
                $subtotal = $quantity * $price;

                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'inventory_id' => $inventory->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;

                // Deduct stock if order is completed
                if ($order->order_status === 'Completed') {
                    $inventory->deductStock($quantity);
                }
            }

            // Update order total
            $order->total_amount = $total;
            $order->save();
        }
    }
}

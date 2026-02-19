<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inventoryItems = [
            ['item_name' => '5 Gallon Water', 'category' => 'Water', 'unit_price' => 25.00, 'quantity_in_stock' => 100, 'reorder_level' => 20, 'supplier_name' => 'Pure Water Supply Co.'],
            ['item_name' => '1 Gallon Water', 'category' => 'Water', 'unit_price' => 15.00, 'quantity_in_stock' => 150, 'reorder_level' => 30, 'supplier_name' => 'Pure Water Supply Co.'],
            ['item_name' => 'Refill (5 Gallon)', 'category' => 'Water', 'unit_price' => 20.00, 'quantity_in_stock' => 200, 'reorder_level' => 40, 'supplier_name' => 'Pure Water Supply Co.'],
            ['item_name' => '5 Gallon Container', 'category' => 'Container', 'unit_price' => 150.00, 'quantity_in_stock' => 50, 'reorder_level' => 10, 'supplier_name' => 'Container Depot'],
            ['item_name' => '1 Gallon Container', 'category' => 'Container', 'unit_price' => 50.00, 'quantity_in_stock' => 75, 'reorder_level' => 15, 'supplier_name' => 'Container Depot'],
            ['item_name' => 'Water Dispenser', 'category' => 'Supplies', 'unit_price' => 500.00, 'quantity_in_stock' => 20, 'reorder_level' => 5, 'supplier_name' => 'Home Appliance Store'],
            ['item_name' => 'Bottle Cap', 'category' => 'Supplies', 'unit_price' => 2.00, 'quantity_in_stock' => 500, 'reorder_level' => 100, 'supplier_name' => 'General Supplies Inc.'],
            ['item_name' => 'Bottle Label', 'category' => 'Supplies', 'unit_price' => 1.50, 'quantity_in_stock' => 1000, 'reorder_level' => 200, 'supplier_name' => 'Printing Solutions'],
            ['item_name' => 'Cleaning Solution', 'category' => 'Supplies', 'unit_price' => 75.00, 'quantity_in_stock' => 30, 'reorder_level' => 10, 'supplier_name' => 'Chemical Supplies Co.'],
            ['item_name' => 'Water Filter', 'category' => 'Supplies', 'unit_price' => 250.00, 'quantity_in_stock' => 15, 'reorder_level' => 5, 'supplier_name' => 'Filtration Systems Ltd.'],
        ];

        foreach ($inventoryItems as $item) {
            \App\Models\Inventory::create($item);
        }
    }
}

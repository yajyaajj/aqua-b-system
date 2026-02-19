<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->enum('order_type', ['Walk-in', 'Delivery', 'Refill']);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('order_status', ['Pending', 'Completed', 'Cancelled'])->default('Pending');
            $table->enum('payment_status', ['Unpaid', 'Partial', 'Paid'])->default('Unpaid');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('order_date');
            $table->timestamps();
            $table->softDeletes();
            
            // Add indexes
            $table->index('order_number');
            $table->index('order_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique();
                $table->foreignId('restaurant_table_id')->constrained('restaurant_tables');
                $table->foreignId('waiter_id')->constrained('users');
                $table->enum('status', ['draft', 'confirmed', 'preparing', 'ready', 'paid', 'cancelled'])->default('draft');
                $table->text('notes')->nullable();
                $table->decimal('subtotal', 10, 2)->default(0);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->decimal('service_charge_amount', 10, 2)->default(0);
                $table->decimal('tax_amount', 10, 2)->default(0);
                $table->decimal('total', 10, 2)->default(0);
                $table->decimal('total_paid', 10, 2)->default(0);
                $table->decimal('remaining_amount', 10, 2)->default(0);
                $table->timestamp('confirmed_at')->nullable();
                $table->timestamp('ready_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};


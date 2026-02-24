<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
                $table->string('item_name');
                $table->decimal('item_price', 10, 2);
                $table->unsignedInteger('quantity')->default(1);
                $table->text('notes')->nullable();
                $table->decimal('subtotal', 10, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};


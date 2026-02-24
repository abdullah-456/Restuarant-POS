<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubtotalToOrderItemsTable extends Migration // This will have a unique name with timestamp
{
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0)->after('notes');
            }
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('subtotal');
        });
    }
}
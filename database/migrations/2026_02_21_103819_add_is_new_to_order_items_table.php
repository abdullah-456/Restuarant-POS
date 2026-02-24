<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsNewToOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'is_new')) {
                $table->boolean('is_new')->default(false)->after('notes');
            }
            
            if (!Schema::hasColumn('order_items', 'added_at')) {
                $table->timestamp('added_at')->nullable()->after('is_new');
            }
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['is_new', 'added_at']);
        });
    }
}
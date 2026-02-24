<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // 1) Drop the WRONG FK + column: orders.table_id -> tables.id
            if (Schema::hasColumn('orders', 'table_id')) {
                // FK name from your error: orders_table_id_foreign
                $table->dropForeign('orders_table_id_foreign');
                $table->dropColumn('table_id');
            }

            // 2) Ensure restaurant_table_id exists (create if missing)
            if (!Schema::hasColumn('orders', 'restaurant_table_id')) {
                $table->unsignedBigInteger('restaurant_table_id')->nullable()->after('order_number');
            }

            // 3) Drop existing FK on restaurant_table_id if it exists (safe attempt)
            // Laravel's default FK name would be: orders_restaurant_table_id_foreign
            // If it doesn't exist, this may throw on some DBs; if so, remove this line.
            try {
                $table->dropForeign('orders_restaurant_table_id_foreign');
            } catch (\Throwable $e) {
                // ignore if not exists
            }

            // 4) Add the correct FK: orders.restaurant_table_id -> restaurant_tables.id
            $table->foreign('restaurant_table_id')
                  ->references('id')
                  ->on('restaurant_tables')
                  ->cascadeOnDelete();

            // Optional: if you want it required, uncomment after cleaning old data:
            // $table->unsignedBigInteger('restaurant_table_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // rollback correct FK
            if (Schema::hasColumn('orders', 'restaurant_table_id')) {
                try {
                    $table->dropForeign('orders_restaurant_table_id_foreign');
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            // recreate old column (NOT recommended, but this is rollback)
            if (!Schema::hasColumn('orders', 'table_id')) {
                $table->unsignedBigInteger('table_id')->nullable();
                // Old FK was wrong for your setup; still restore it if you rollback:
                $table->foreign('table_id')->references('id')->on('tables');
            }
        });
    }
};
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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_paid', 10, 2)->default(0)->after('total');
            $table->decimal('remaining_amount', 10, 2)->default(0)->after('total_paid');
            // optional but useful:
            // $table->timestamp('paid_at')->nullable()->after('remaining_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_paid', 'remaining_amount'/*, 'paid_at'*/]);
        });
    }
};

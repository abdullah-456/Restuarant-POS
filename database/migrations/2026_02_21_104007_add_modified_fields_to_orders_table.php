<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModifiedFieldsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'modified_at')) {
                $table->timestamp('modified_at')->nullable()->after('completed_at');
            }
            if (!Schema::hasColumn('orders', 'modified_by')) {
                $table->unsignedBigInteger('modified_by')->nullable()->after('modified_at');
                $table->foreign('modified_by')->references('id')->on('users');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['modified_by']);
            $table->dropColumn(['modified_at', 'modified_by']);
        });
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('stock_batches', function (Blueprint $table) {
        $table->integer('remaining')->after('quantity');
    });
}

public function down()
{
    Schema::table('stock_batches', function (Blueprint $table) {
        $table->dropColumn('remaining');
    });
}

};

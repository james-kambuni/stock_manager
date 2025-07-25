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
        $table->unsignedBigInteger('purchase_id')->after('product_id');

        // Optional FK
        $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('stock_batches', function (Blueprint $table) {
        $table->dropForeign(['purchase_id']);
        $table->dropColumn('purchase_id');
    });
}

};

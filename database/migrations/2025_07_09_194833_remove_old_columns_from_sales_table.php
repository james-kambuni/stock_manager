<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['product_id']);

            // Now drop the columns
            $table->dropColumn(['product_id', 'quantity', 'unit_price']);
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();

            // Re-add foreign key if needed
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};

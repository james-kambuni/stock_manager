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
    Schema::table('purchases', function (Blueprint $table) {
        $table->date('expiry_date')->nullable()->change();
    });

    Schema::table('stock_batches', function (Blueprint $table) {
        $table->date('expiry_date')->nullable()->change();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

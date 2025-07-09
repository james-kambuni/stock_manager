<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dateTime('sale_date')->after('id')->nullable();
            $table->decimal('total', 10, 2)->after('sale_date')->default(0);
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['sale_date', 'total']);
        });
    }
};

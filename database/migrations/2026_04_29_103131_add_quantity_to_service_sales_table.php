<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_sales', function (Blueprint $table) {
            $table->integer('quantity')->default(1)->after('service_id');
            $table->decimal('unit_price', 10, 2)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('service_sales', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'unit_price']);
        });
    }
};

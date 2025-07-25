<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
    Schema::create('sales', function (Blueprint $table) {
        $table->id();
        $table->dateTime('sale_date')->default(now());
        $table->decimal('total', 10, 2)->nullable();
        $table->timestamps();
    });
}

    public function down(): void {
        Schema::dropIfExists('sales');
    }
};

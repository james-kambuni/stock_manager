<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tenant_id'); // <--- Important for multi-tenancy
    $table->string('invoice_number')->unique();
    $table->string('customer_name');
    $table->string('customer_address')->nullable();
    $table->string('customer_phone');
    $table->string('customer_email')->nullable();
    $table->decimal('subtotal', 10, 2);
    $table->decimal('vat', 10, 2);
    $table->decimal('total', 10, 2);
    $table->string('served_by');
    $table->timestamps();

    // Foreign key constraint (optional but recommended)
    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

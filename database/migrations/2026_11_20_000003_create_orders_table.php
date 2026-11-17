<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();           // contoh: INV-20261120-0001
            $table->foreignId('user_id')->constrained()->restrictOnDelete(); // kasir pencatat
            $table->enum('channel', ['dine_in', 'takeaway', 'online', 'frozen']);
            $table->enum('status', ['pending', 'diproses', 'selesai', 'dibatalkan'])->default('pending');
            // Pembayaran diisi saat pesanan diselesaikan (alur toko: bayar di akhir).
            $table->enum('payment_method', ['tunai', 'qris'])->nullable();
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('amount_paid')->default(0);
            $table->unsignedInteger('change_amount')->default(0);
            $table->string('note')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('payment_method');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->unsignedInteger('price');     // snapshot harga saat transaksi
            $table->unsignedInteger('subtotal');  // qty x price
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};

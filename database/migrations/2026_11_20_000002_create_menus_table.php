<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name')->unique();
            $table->unsignedInteger('price');                 // rupiah, tanpa desimal
            $table->unsignedInteger('stock')->default(0);     // stok siap jual hari ini
            $table->unsignedInteger('min_stock')->default(10); // ambang peringatan stok menipis
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_reservasi', function (Blueprint $table) {
            $table->id('id_detail_reservasi');
            $table->foreignId('id_reservasi')->constrained('reservasi', 'id_reservasi')->cascadeOnDelete();
            $table->foreignId('id_menu')->constrained('menu', 'id_menu')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->integer('harga');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_reservasi');
    }
};

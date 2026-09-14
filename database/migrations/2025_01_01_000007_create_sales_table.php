<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->foreignId('id_user')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->foreignId('id_reservasi')->nullable()->constrained('reservasi', 'id_reservasi')->nullOnDelete();
            $table->string('kode')->nullable()->unique();
            $table->integer('total')->default(0);
            $table->json('daftar_item')->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->string('status_pembayaran')->default('pending');
            $table->string('nama_pelanggan')->nullable();
            $table->integer('jumlah_bayar')->default(0);
            $table->integer('kembalian')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};

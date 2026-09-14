<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id('id_reservasi');
            $table->foreignId('id_user')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->foreignId('id_meja')->nullable()->constrained('meja', 'id_meja')->nullOnDelete();
            $table->string('kode_reservasi')->unique();
            $table->string('nama_pelanggan');
            $table->string('email_pelanggan')->nullable();
            $table->string('telepon_pelanggan')->nullable();
            $table->unsignedTinyInteger('jumlah_orang');
            $table->date('tanggal_reservasi');
            $table->time('jam_reservasi');
            $table->text('catatan_reservasi')->nullable();
            $table->unsignedTinyInteger('nomor_meja')->nullable();
            $table->string('lokasi_reservasi')->nullable();
            $table->json('item_pesanan')->nullable();
            $table->unsignedInteger('total_harga')->default(0);
            $table->unsignedInteger('jumlah_dp')->default(0);
            $table->string('status_dp')->default('belum_bayar');
            $table->unsignedInteger('sisa_pembayaran')->default(0);
            $table->integer('biaya_pembatalan')->nullable();
            $table->enum('status_reservasi', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index(['tanggal_reservasi', 'status_reservasi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};

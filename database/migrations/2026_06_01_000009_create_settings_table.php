<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value')->nullable();
            $table->timestamps();
        });

        DB::table('settings')->insert([
            ['key' => 'pajak', 'value' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'service', 'value' => '5', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'jam_buka', 'value' => '08:00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'jam_tutup', 'value' => '21:00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

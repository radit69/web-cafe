<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedInteger('dp_amount')->default(0)->after('total_amount');
            $table->string('dp_status')->default('unpaid')->after('dp_amount');
            $table->unsignedInteger('remaining_amount')->default(0)->after('dp_status');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['dp_amount', 'dp_status', 'remaining_amount']);
        });
    }
};

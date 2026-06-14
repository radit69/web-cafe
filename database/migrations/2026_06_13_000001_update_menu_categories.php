<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('menus')->where('category', 'maincourse')->update(['category' => 'main course']);
        DB::table('menus')->where('category', 'Main Course')->update(['category' => 'main course']);
        DB::table('menus')->where('category', 'Salad')->update(['category' => 'dessert']);
    }

    public function down()
    {
    }
};

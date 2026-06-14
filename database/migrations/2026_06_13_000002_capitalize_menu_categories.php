<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('menus')->where('category', 'coffee')->update(['category' => 'Coffee']);
        DB::table('menus')->where('category', 'non-coffee')->update(['category' => 'Non-Coffee']);
        DB::table('menus')->where('category', 'main course')->update(['category' => 'Main Course']);
        DB::table('menus')->where('category', 'dessert')->update(['category' => 'Dessert']);
    }

    public function down()
    {
    }
};

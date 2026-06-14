<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // Contoh data awal untuk menu & inventory
        if (Menu::count() === 0) {
            Menu::insert([
                [
                    'name'        => 'Hot Latte',
                    'category'    => 'coffee',
                    'description' => 'Kopi latte panas dengan espresso dan steamed milk.',
                    'price'       => 35000,
                    'stock'       => 100,
                    'status'      => 'tersedia',
                    'image'       => 'menu_images/hot-latte.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'name'        => 'Croissant Matcha',
                    'category'    => 'non-coffee',
                    'description' => 'Croissant isian matcha cream yang lembut.',
                    'price'       => 50000,
                    'stock'       => 60,
                    'status'      => 'tersedia',
                    'image'       => 'menu_images/croissant-matcha.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'name'        => 'Berry Cake',
                    'category'    => 'dessert',
                    'description' => 'Cake lembut dengan topping buah berry segar.',
                    'price'       => 65000,
                    'stock'       => 40,
                    'status'      => 'habis',
                    'image'       => 'menu_images/berry-cake.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'name'        => 'Pie Cheesy',
                    'category'    => 'non-coffee',
                    'description' => 'Pie gurih dengan isian keju creamy.',
                    'price'       => 45000,
                    'stock'       => 95,
                    'status'      => 'tersedia',
                    'image'       => 'menu_images/pie-cheesy.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'name'        => 'Americano',
                    'category'    => 'coffee',
                    'description' => 'Espresso klasik dengan tambahan air panas.',
                    'price'       => 30000,
                    'stock'       => 90,
                    'status'      => 'habis',
                    'image'       => 'menu_images/americano.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'name'        => 'Cappuccino',
                    'category'    => 'coffee',
                    'description' => 'Kopi dengan espresso, steamed milk, dan foam tebal.',
                    'price'       => 35000,
                    'stock'       => 100,
                    'status'      => 'tersedia',
                    'image'       => 'menu_images/cappuccino.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
            ]);
        }
    }
}

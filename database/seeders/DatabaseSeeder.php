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
        $this->call(UserSeeder::class);
        $this->call(TableSeeder::class);

        // Contoh data awal untuk menu & inventory
        if (Menu::count() === 0) {
            Menu::insert([
                [
                    'nama_menu'   => 'Hot Latte',
                    'kategori'    => 'coffee',
                    'deskripsi'   => 'Kopi latte panas dengan espresso dan steamed milk.',
                    'harga'       => 35000,
                    'stok'        => 100,
                    'status'      => 'tersedia',
                    'gambar'      => 'menu_images/hot-latte.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'nama_menu'   => 'Croissant Matcha',
                    'kategori'    => 'non-coffee',
                    'deskripsi'   => 'Croissant isian matcha cream yang lembut.',
                    'harga'       => 50000,
                    'stok'        => 60,
                    'status'      => 'tersedia',
                    'gambar'      => 'menu_images/croissant-matcha.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'nama_menu'   => 'Berry Cake',
                    'kategori'    => 'dessert',
                    'deskripsi'   => 'Cake lembut dengan topping buah berry segar.',
                    'harga'       => 65000,
                    'stok'        => 40,
                    'status'      => 'habis',
                    'gambar'      => 'menu_images/berry-cake.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'nama_menu'   => 'Pie Cheesy',
                    'kategori'    => 'non-coffee',
                    'deskripsi'   => 'Pie gurih dengan isian keju creamy.',
                    'harga'       => 45000,
                    'stok'        => 95,
                    'status'      => 'tersedia',
                    'gambar'      => 'menu_images/pie-cheesy.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'nama_menu'   => 'Americano',
                    'kategori'    => 'coffee',
                    'deskripsi'   => 'Espresso klasik dengan tambahan air panas.',
                    'harga'       => 30000,
                    'stok'        => 90,
                    'status'      => 'habis',
                    'gambar'      => 'menu_images/americano.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'nama_menu'   => 'Cappuccino',
                    'kategori'    => 'coffee',
                    'deskripsi'   => 'Kopi dengan espresso, steamed milk, dan foam tebal.',
                    'harga'       => 35000,
                    'stok'        => 100,
                    'status'      => 'tersedia',
                    'gambar'      => 'menu_images/cappuccino.jpg',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
            ]);
        }
    }
}

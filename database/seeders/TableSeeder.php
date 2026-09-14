<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            if ($i <= 5) {
                $capacity = 2;
            } elseif ($i <= 10) {
                $capacity = 4;
            } else {
                $capacity = 6;
            }

            Table::updateOrCreate(
                ['nomor_meja' => $i],
                [
                    'kapasitas' => $capacity,
                    'status_meja' => 'available',
                ]
            );
        }
    }
}

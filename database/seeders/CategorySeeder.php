<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Pempek Reguler' => 'Kapal Selam dan Lenjer ukuran besar/sedang',
            'Pempek Mini'    => 'Pempek mini serba Rp2.000',
            'Menu Lainnya'   => 'Lenggang dan Tekwan',
            'Pelengkap & Minuman' => 'Cuko botolan, kerupuk kemplang, es/teh manis',
        ] as $name => $desc) {
            Category::updateOrCreate(['name' => $name], ['description' => $desc]);
        }
    }
}

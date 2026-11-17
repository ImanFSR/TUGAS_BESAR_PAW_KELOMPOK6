<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // [kategori => [[nama, harga, stok awal demo]]]  -- harga sesuai hasil wawancara
        $data = [
            'Pempek Reguler' => [
                ['Kapal Selam Besar', 10000, 30], ['Kapal Selam Sedang', 5000, 40], ['Lenjer Besar', 10000, 30],
            ],
            'Pempek Mini' => [
                ['Selam Mini', 2000, 60], ['Adaan', 2000, 60], ['Lenjer Mini', 2000, 60], ['Kulit', 2000, 60],
                ['Udang', 2000, 60], ['Keju', 2000, 60], ['Kriting', 2000, 60],
            ],
            'Menu Lainnya' => [['Lenggang', 10000, 20], ['Tekwan', 10000, 20]],
            'Pelengkap & Minuman' => [
                ['Cuko Botolan', 5000, 25], ['Kerupuk Kemplang', 5000, 25],
                ['Es Teh Manis', 3000, 50], ['Es Jeruk', 5000, 50],
            ],
        ];

        foreach ($data as $category => $menus) {
            $cat = Category::where('name', $category)->firstOrFail();
            foreach ($menus as [$name, $price, $stock]) {
                Menu::updateOrCreate(['name' => $name], [
                    'category_id' => $cat->id, 'price' => $price, 'stock' => $stock, 'min_stock' => 10,
                ]);
            }
        }
    }
}

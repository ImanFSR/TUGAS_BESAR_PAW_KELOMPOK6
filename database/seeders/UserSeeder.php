<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo (dipakai saat presentasi). Password di-hash otomatis oleh cast 'hashed'.
        User::updateOrCreate(['email' => 'owner@bututin.test'], [
            'name' => 'Ibu Titin', 'password' => 'password', 'role' => 'owner', 'is_active' => true,
        ]);
        User::updateOrCreate(['email' => 'kasir@bututin.test'], [
            'name' => 'Staf Kasir', 'password' => 'password', 'role' => 'staf', 'is_active' => true,
        ]);
    }
}

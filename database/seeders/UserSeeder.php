<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    \App\Models\User::create([
        'name' => 'Admin User',
        'email' => 'admin@proyek.com',
        'password' => bcrypt('password123'),
        'role' => 'admin',
    ]);

    \App\Models\User::create([
        'name' => 'Anggota Biasa',
        'email' => 'anggota@proyek.com',
        'password' => bcrypt('password123'),
        'role' => 'anggota',
    ]);
    }
}

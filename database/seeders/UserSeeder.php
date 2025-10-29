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
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@proyek.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'anggota@proyek.com'],
            [
                'name' => 'Anggota Biasa',
                'password' => bcrypt('password123'),
                'role' => 'anggota',
            ]
        );
    }
}

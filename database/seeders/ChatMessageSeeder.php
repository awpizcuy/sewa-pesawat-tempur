<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChatMessage;
use App\Models\Rental;

class ChatMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil rental pertama untuk testing
        $rental = Rental::first();
        
        if ($rental) {
            // Buat beberapa pesan chat untuk testing
            ChatMessage::create([
                'rental_id' => $rental->id,
                'user_id' => $rental->user_id,
                'sender' => 'user',
                'message' => 'Halo admin, saya ingin bertanya tentang pengembalian unit ini.'
            ]);

            ChatMessage::create([
                'rental_id' => $rental->id,
                'user_id' => $rental->user_id,
                'sender' => 'admin',
                'message' => 'Halo! Silakan sampaikan pertanyaan Anda. Saya siap membantu.'
            ]);

            ChatMessage::create([
                'rental_id' => $rental->id,
                'user_id' => $rental->user_id,
                'sender' => 'user',
                'message' => 'Kapan saya bisa mengembalikan unit ini?'
            ]);

            $this->command->info('Chat messages created for rental ID: ' . $rental->id);
        } else {
            $this->command->warn('No rental found. Please run rental seeder first.');
        }
    }
}
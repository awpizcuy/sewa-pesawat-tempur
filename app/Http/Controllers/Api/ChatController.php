<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Rental;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getMessages(Request $request, $rentalId)
    {
        // Pastikan rental milik user yang login
        $rental = Rental::where('id', $rentalId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $messages = ChatMessage::where('rental_id', $rentalId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request, $rentalId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Pastikan rental milik user yang login
        $rental = Rental::where('id', $rentalId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $message = ChatMessage::create([
            'rental_id' => $rentalId,
            'user_id' => auth()->id(),
            'sender' => 'user',
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => 'Pesan berhasil dikirim',
            'data' => $message->load('user')
        ]);
    }

    public function getAdminMessages(Request $request, $rentalId)
    {
        try {
            // Pastikan user adalah admin
            if (auth()->user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $rental = Rental::with(['user', 'unit'])->findOrFail($rentalId);

            $messages = ChatMessage::where('rental_id', $rentalId)
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'rental' => $rental,
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            \Log::error('Chat admin error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error loading chat messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendAdminMessage(Request $request, $rentalId)
    {
        // Pastikan user adalah admin
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $rental = Rental::findOrFail($rentalId);

        $message = ChatMessage::create([
            'rental_id' => $rentalId,
            'user_id' => $rental->user_id,
            'sender' => 'admin',
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => 'Pesan admin berhasil dikirim',
            'data' => $message->load('user')
        ]);
    }
}
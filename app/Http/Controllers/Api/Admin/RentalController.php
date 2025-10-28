<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Unit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    /**
     * (Req 14) Menampilkan daftar semua unit yang sedang dipinjam ('rented' atau 'overdue').
     */
    public function index()
    {
        $rentals = Rental::with(['user:id,name,email', 'unit:id,unit_code,name'])
                         ->whereIn('status', ['rented', 'overdue'])
                         ->latest('rent_date')
                         ->get();

        return response()->json($rentals);
    }

    /**
     * (Req 13) Memproses pengembalian unit oleh Admin.
     * (Req 12) Menghitung denda jika terlambat.
     */
    public function processReturn(Request $request, Rental $rental)
    {
        if ($rental->status != 'rented' && $rental->status != 'overdue') {
            return response()->json(['message' => 'Unit ini tidak sedang dalam status dipinjam.'], 400);
        }

        $returnDate = Carbon::now();
        $dueDate = $rental->due_date;
        $fineAmount = 0;

        // 2. Hitung Denda (Req 12)
        if ($returnDate->isAfter($dueDate)) {
            $lateDays = $returnDate->diffInDays($dueDate);
            $finePerDay = 100000;
            $fineAmount = $lateDays * $finePerDay;
        }

        DB::beginTransaction();
        try {
            // 3. Update data rental
            $rental->update([
                'return_date' => $returnDate,
                'status' => 'returned',
                'fine_amount' => $fineAmount,
            ]);

            $unit = $rental->unit;
            if ($unit->status == 'rented') {
                 $unit->update([
                     'stock' => 1,
                     'status' => 'available'
                 ]);
            } else {
                 $unit->increment('stock');
            }

            DB::commit();

            return response()->json([
                'message' => 'Unit berhasil dikembalikan.',
                'rental' => $rental->load('unit', 'user')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal memproses pengembalian.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * (Req 16) Menampilkan riwayat peminjaman seorang user.
     */
    public function userRentalHistory(Request $request, $userId)
    {
         $user = \App\Models\User::findOrFail($userId);

         $history = Rental::with('unit:id,unit_code,name')
                           ->where('user_id', $userId)
                           ->latest('rent_date')
                           ->get();

         return response()->json([
             'user' => [
                 'id' => $user->id,
                 'name' => $user->name,
                 'email' => $user->email,
             ],
             'history' => $history
         ]);
    }
}

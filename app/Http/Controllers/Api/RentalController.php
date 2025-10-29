<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Unit;
use App\Http\Requests\StoreRentalRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    /**
     */
    public function store(StoreRentalRequest $request)
    {

        $validated = $request->validated();
        $user = $request->user();
        $unit = Unit::find($validated['unit_id']);

        $rentDate = Carbon::parse($validated['rent_date'] ?? Carbon::now());
        $dueDate = Carbon::parse($validated['due_date'] ?? $rentDate->copy()->addDays(5));

        DB::beginTransaction();
        try {
            $rental = Rental::create([
                'user_id' => $user->id,
                'unit_id' => $unit->id,
                'rent_date' => $rentDate,
                'due_date' => $dueDate,
                'status' => 'rented',
                'booking_code' => 'BK'.now()->format('YmdHis').rand(100,999),
                'borrower_name' => $validated['borrower_name'],
                'borrower_identity_number' => $validated['borrower_identity_number'],
                'payment_method' => $validated['payment_method'],
                'total_amount' => $validated['total_amount'],
            ]);

            if ($unit->stock > 1) {
                $unit->decrement('stock');
            } else {
                $unit->update([
                    'stock' => 0,
                    'status' => 'rented'
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Unit berhasil dipinjam.',
                'rental' => $rental->load('unit', 'user')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal melakukan peminjaman.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     */
    public function myRentals(Request $request)
    {
        $user = $request->user();
        $rentals = $user->rentals()
                        ->with('unit')
                        ->orderByDesc('rent_date')
                        ->orderByDesc('return_date')
                        ->get();

        return response()->json($rentals);
    }

    /**
     * Riwayat peminjaman yang sudah selesai (returned)
     */
    public function rentalHistory(Request $request)
    {
        $user = $request->user();
        $rentals = $user->rentals()
                        ->with('unit')
                        ->where('status', 'returned')
                        ->latest('return_date')
                        ->get();

        return response()->json($rentals);
    }

    /**
     * Konfirmasi pembayaran denda oleh user.
     * Tidak mengubah status peminjaman; hanya melakukan validasi sederhana
     * dan mengembalikan respons sukses sebagai konfirmasi.
     */
    public function payFine(Request $request, Rental $rental)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:transfer,ewallet,cash'
        ]);

        // Pastikan peminjaman milik user yang sedang login
        if ($rental->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Cek keterlambatan (status overdue atau sudah melewati due_date)
        $now = Carbon::now();
        $isOverdue = $rental->status === 'overdue' || ($rental->status === 'rented' && $now->gt($rental->due_date));
        if (!$isOverdue) {
            return response()->json(['message' => 'Denda tidak berlaku untuk peminjaman ini'], 422);
        }

        // Validasi jumlah denda: tetap Rp 7.500.000 sesuai requirement
        if ((int)$request->input('amount') !== 7500000) {
            return response()->json(['message' => 'Jumlah denda tidak sesuai'], 422);
        }

        // Proses pengembalian + simpan denda
        DB::beginTransaction();
        try {
            $rental->update([
                'return_date' => $now,
                'status' => 'returned',
                'fine_amount' => 7500000,
            ]);

            // Kembalikan stok unit
            $unit = $rental->unit ?: Unit::find($rental->unit_id);
            if ($unit) {
                if ($unit->status === 'rented') {
                    $unit->update(['stock' => 1, 'status' => 'available']);
                } else {
                    $unit->increment('stock');
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Pembayaran denda dikonfirmasi dan peminjaman diselesaikan',
                'rental' => $rental->load('unit','user')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal memproses pembayaran denda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function returnUnit(Request $request, Rental $rental)
    {
        // Pastikan rental milik user yang login
        if ($rental->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Pastikan status bisa dikembalikan
        if (!in_array($rental->status, ['rented', 'overdue'])) {
            return response()->json(['message' => 'Unit tidak bisa dikembalikan'], 400);
        }

        // Update status rental
        $rental->update([
            'status' => 'returned',
            'return_date' => Carbon::now(),
        ]);

        // Update stock unit
        $unit = $rental->unit;
        $unit->increment('stock');
        
        // Jika stock > 0, ubah status unit jadi available
        if ($unit->stock > 0) {
            $unit->update(['status' => 'available']);
        }

        return response()->json(['message' => 'Unit berhasil dikembalikan.']);
    }
}

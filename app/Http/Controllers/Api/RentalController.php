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

        $rentDate = Carbon::now();
        $dueDate = $rentDate->copy()->addDays(5);

        DB::beginTransaction();
        try {
            $rental = Rental::create([
                'user_id' => $user->id,
                'unit_id' => $unit->id,
                'rent_date' => $rentDate,
                'due_date' => $dueDate,
                'status' => 'rented',
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
                        ->whereIn('status', ['rented', 'overdue'])
                        ->latest('rent_date')
                        ->get();

        return response()->json($rentals);
    }
}

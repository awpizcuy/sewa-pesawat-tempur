<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Unit;
use Carbon\Carbon;

class StoreRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role == 'anggota';
    }

    public function rules(): array
    {
        return [
            'unit_id' => 'required|exists:units,id',
            'borrower_name' => 'required|string|max:255',
            'borrower_identity_number' => 'required|string|max:100',
            'rent_date' => 'required|date',
            'due_date' => 'required|date|after:rent_date',
            'payment_method' => 'required|in:ewallet,transfer,va',
            'total_amount' => 'required|numeric|min:0',
        ];
    }

    /**
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Ambil data unit yang mau dipinjam
            $unit = Unit::find($this->input('unit_id'));
            $user = $this->user();

            // 1. Cek Ketersediaan Unit
            if ($unit && ($unit->status != 'available' || $unit->stock <= 0)) {
                $validator->errors()->add('unit_id', 'Unit tidak tersedia atau stok habis.');
            }

            // 2. Cek Batas Maksimal Peminjaman (Req 11)
            $currentRentalsCount = $user->rentals()->where('status', 'rented')->count();
            if ($currentRentalsCount >= 2) {
                $validator->errors()->add('user_id', 'Anda sudah mencapai batas maksimal 2 unit peminjaman.');
            }

            // 3. (Opsional tapi bagus) Cek apakah unit ini sudah dipinjam user?
            $isAlreadyRentedByUser = $user->rentals()
                ->where('unit_id', $this->input('unit_id'))
                ->where('status', 'rented')
                ->exists();
            if ($isAlreadyRentedByUser) {
                $validator->errors()->add('unit_id', 'Anda sudah meminjam unit ini.');
            }

        });
    }
}

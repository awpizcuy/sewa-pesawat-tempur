<?php

namespace App\Http\Requests\Admin; // <-- Pastikan namespace benar

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    /**
     */
    public function authorize(): bool
    {
        // Ganti dari false menjadi true
        return true;
    }

    /**
     *
     * @return array<string,
     */
    public function rules(): array
    {
        return [
            'unit_code' => 'required|string|max:50|unique:units,unit_code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:1',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ];
    }
}

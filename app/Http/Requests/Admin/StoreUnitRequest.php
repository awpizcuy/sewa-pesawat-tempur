<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unit_code' => 'required|string|max:50|unique:units,unit_code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'price_per_day' => 'required|numeric|min:0',
            'status' => 'required|string|in:available,rented',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ];
    }
}

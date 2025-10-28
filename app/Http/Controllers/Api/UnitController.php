<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     */
    public function index()
    {
        $units = Unit::with('categories')
                    ->where('status', 'available')
                    ->latest()
                    ->get();

        return response()->json($units);
    }

    /**
     */
    public function search(Request $request)
    {
        $request->validate(['name' => 'required|string']);

        $name = $request->input('name');

        $units = Unit::with('categories')
                    ->where('status', 'available')
                    ->where('name', 'LIKE', "%{$name}%")
                    ->get();

        return response()->json($units);
    }
}

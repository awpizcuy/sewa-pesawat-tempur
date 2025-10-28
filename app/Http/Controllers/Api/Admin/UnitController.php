<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Http\Requests\Admin\StoreUnitRequest;
use App\Http\Requests\Admin\UpdateUnitRequest;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     */
    public function index()
    {
        $units = Unit::with('categories')->latest()->get();
        return response()->json($units);
    }

    /**
     */
    public function store(StoreUnitRequest $request)
    {
        $validated = $request->validated();

        $unit = Unit::create([
            'unit_code' => $validated['unit_code'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'stock' => $validated['stock'],
        ]);

        $unit->categories()->attach($validated['categories']);

        return response()->json([
            'message' => 'Unit created successfully',
            'unit' => $unit->load('categories')
        ], 201);
    }

    /**
     */
    public function show(Unit $unit)
    {
        return response()->json($unit->load('categories'));
    }

    /**
     */
    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $validated = $request->validated();

        $unit->update([
            'unit_code' => $validated['unit_code'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'stock' => $validated['stock'],
        ]);

        $unit->categories()->sync($validated['categories']);

        return response()->json([
            'message' => 'Unit updated successfully',
            'unit' => $unit->load('categories')
        ]);
    }

    /**
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return response()->json([
            'message' => 'Unit deleted successfully'
        ], 200);
    }
}

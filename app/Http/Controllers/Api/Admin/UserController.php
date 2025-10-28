<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user (anggota).
     */
    public function index()
    {
        $users = User::where('role', 'anggota')->latest()->get();
        return response()->json($users);
    }

    /**
     * Menyimpan user (anggota) baru.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => 'anggota',
        ]);

        return response()->json([
            'message' => 'Anggota created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Menampilkan detail satu user (anggota).
     */
    public function show(User $user)
    {
        if ($user->role !== 'anggota') {
             return response()->json(['message' => 'User not found or not an anggota.'], 404);
        }
        return response()->json($user);
    }

    /**
     * Mengupdate data user (anggota).
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($user->role !== 'anggota') {
             return response()->json(['message' => 'User not found or not an anggota.'], 404);
        }

        $validated = $request->validated();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'address' => $validated['address'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return response()->json([
            'message' => 'Anggota updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Menghapus user (anggota).
     */
    public function destroy(User $user)
    {
        if ($user->role !== 'anggota') {
             return response()->json(['message' => 'User not found or not an anggota.'], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'Anggota deleted successfully'
        ], 200);
    }
}

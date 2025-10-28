<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Registrasi user baru.
     * (Req 4: User terdaftar sebagai anggota)
     */
    public function register(Request $request)
    {
        // Validasi data (Req 17: Validasi)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'sometimes|string|in:admin,anggota', // 'sometimes' berarti opsional
        ]);

        // Buat user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'] ?? 'anggota', // Default role adalah 'anggota'
        ]);

        // Beri respons
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201); // 201 = Created
    }

    /**
     * Login user.
     * (Req 3: User Harus Melakukan Login)
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Coba lakukan login
        if (!Auth::attempt($credentials)) {
            // Jika gagal
            return response()->json([
                'message' => 'Email or password incorrect'
            ], 401); // 401 = Unauthorized
        }

        // Jika berhasil
        $user = $request->user();

        // Buat token (Kunci untuk akses API nanti)
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang dipakai
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}

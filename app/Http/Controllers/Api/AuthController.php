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
            'member_identity_number' => 'required|string|max:255',
            'password' => ['required', 'confirmed', 'min:6'],
            'role' => 'sometimes|string|in:admin,anggota', // 'sometimes' berarti opsional
        ]);

        // Daftar kode identitas anggota yang valid
        $validCodes = ['AU0598', 'AU9598', 'AU9805', 'AU9895', 'AU9505', 'AU0595'];
        
        // Validasi kode identitas
        $memberCode = strtoupper(trim($validatedData['member_identity_number']));
        
        // Cek apakah kode sesuai dengan yang terdaftar
        if (!in_array($memberCode, $validCodes)) {
            return response()->json([
                'message' => 'Kode tidak sesuai gagal register'
            ], 422);
        }
        
        // Cek apakah kode sudah digunakan
        $existingUser = User::where('member_identity_number', $memberCode)->first();
        if ($existingUser) {
            return response()->json([
                'message' => 'Kode sudah digunakan'
            ], 422);
        }

        // Buat user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'member_identity_number' => $memberCode,
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'] ?? 'anggota', // Default role adalah 'anggota'
        ]);

        // Beri respons
        return response()->json([
            'message' => 'Berhasil register',
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

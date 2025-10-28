<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfileRequest;

class UserController extends Controller
{
    /**
     */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $validatedData = $request->validated();

        $user->update($validatedData);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
}

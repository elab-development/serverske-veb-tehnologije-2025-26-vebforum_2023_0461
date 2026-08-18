<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $data['email'])->first();

        $token = Str::random(60);
        $user->remember_token = $token;
        $user->save();

        return response()->json([
            'message' => 'Password reset token generated.',
            'token' => $token,
            'email' => $user->email,
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || $user->remember_token !== $data['token']) {
            return response()->json([
                'message' => 'Invalid reset token.',
            ], 422);
        }

        $user->password = Hash::make($data['password']);
        $user->remember_token = null;
        $user->save();

        return response()->json([
            'message' => 'Password reset successful.',
        ]);
    }
}

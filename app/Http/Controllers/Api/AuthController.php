<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Get access token for user.
     */
    public function getToken(Request $request)
    {
        try {
            $data = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (! Auth::attempt($data)) {
                Log::info('[Auth - API] Email atau password salah');

                return response()->json([
                    'message' => 'Email atau password salah',
                ], 401);
            }
            
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('api_token')->plainTextToken;
            
            Log::info("Token generated for user: {$user->email}");

            return response()->json([
                'message' => 'Login berhasil',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat login', [
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

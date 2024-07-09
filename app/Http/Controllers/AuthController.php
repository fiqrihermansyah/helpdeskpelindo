<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'nipp' => 'required|string',
                'password' => 'required|string',
            ]);

            Log::info('Login attempt', ['nipp' => $request->nipp]);

            $credentials = $request->only('nipp', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Check if 'nama' field is present
                if (!$user->nama) {
                    Log::warning('User authenticated but "nama" field is missing', ['user_id' => $user->id]);
                    return response()->json(['message' => 'User data incomplete'], 422);
                }

                Log::info('Authenticated user', ['user' => $user]);

                $token = $user->createToken('auth_token')->plainTextToken;
                $roles = $user->roles->pluck('name');

                return response()->json([
                    'message' => 'Login successful',
                    'roles' => $roles,
                    'nama' => $user->nama,
                    'token' => $token,
                ], 200);
            } else {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json(['message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json(['message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }
}

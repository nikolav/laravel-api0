<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
  // Register a new user
  public function register(Request $request)
  {
    $validated = $request->validate([
      'email'    => 'required|string|email|unique:users',
      'password' => 'required|string',
    ]);

    $user = User::create([
      'email'    => $validated['email'],
      'password' => Hash::make($validated['password']),
    ]);

    $token = $user->createToken('access_token', ['*'])->plainTextToken;

    return response()->json([
      'access_token' => $token,
      'auth'         => $user,
    ], 201);
  }

  // Login user
  public function authenticate(Request $request)
  {
    $request->validate([
      'email'    => 'required|email',
      'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      throw ValidationException::withMessages([
        'auth' => 'Access denied.',
      ]);
    }

    $token = $user->createToken('access_token', ['*'])->plainTextToken;

    return response()->json([
      'access_token' => $token,
      'auth'         => $user,
    ]);
  }

  // Logout user (destroy token)
  public function logout(Request $request)
  {
    // $request->user()->currentAccessToken()->delete();
    $request->user()->tokens()->delete(); # revoke all tokens

    return response()->json([
      'status' => 'ok'
    ]);
  }

  // Get authenticated user data
  public function who(Request $request)
  {
    return response()->json([
      'auth' => $request->user()
    ]);
  }
}

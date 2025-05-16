<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed',
            'phone' => 'nullable|string|max:15', // Add validation for phone
            'birth_date' => 'nullable|date',     // Add validation for birth date
            'address' => 'nullable|string|max:500', // Add validation for address
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,         // Save phone
            'birth_date' => $request->birth_date, // Save birth date
            'address' => $request->address,     // Save address
        ]);
    
        return response()->json([
            "status" => true,
            'message' => 'User registered successfully',
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !password_verify($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            "status" => true,
            'message' => 'User logged in successfully',
            'token' => $token,
        ]);
    }
    public function logout(Request $request)
    {
        request()->user()->tokens()->delete();

        return response()->json([
            "status" => true,
            'message' => 'User logged out successfully',
        ]);
    }

    public function profile(Request $request)
    {
        $userData = auth()->user();

        return response()->json([
            "status" => true,
            'message' => 'Profile information',
            'data' => $userData,
        ]);

    }
}

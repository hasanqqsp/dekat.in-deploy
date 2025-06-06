<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;



class ApiController extends Controller
{
/**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Register a new user",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="password", type="string"),
 *             @OA\Property(property="phone", type="string"),
 *             @OA\Property(property="birth_date", type="string", format="date"),
 *             @OA\Property(property="address", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User registered successfully"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation failed"
 *     )
 * )
 */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
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

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
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
    
        if (!$user || !Hash::check($request->password, $user->password)) {
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
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    
        return response()->json([
            "status" => true,
            'message' => 'User logged out successfully',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Get authenticated user's profile",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile data"
     *     )
     * )
     */
    public function profile(Request $request)
    {
        $userData = auth()->user();

        return response()->json([
            'userId' => $userData->id,
            'profileImage' => $userData->profile_image,
            'name' => $userData->name,
            'phone' => $userData->phone,
            'email' => $userData->email,
            'birthDate' => $userData->birth_date,
        ]);

    }


    /**
     * @OA\Patch(
     *     path="/api/update-password",
     *     summary="Change password for authenticated user",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password","new_password"},
     *             @OA\Property(property="current_password", type="string"),
     *             @OA\Property(property="new_password", type="string", minLength=8)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Current password is incorrect"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();

        if (!password_verify($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect',
            ], 401);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json([
            "status" => true,
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/update-profile",
     *     summary="Update authenticated user's profile",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="birth_date", type="string", format="date"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="profile_image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|nullable|string|max:15', // Validate only if present
            'birth_date' => 'sometimes|nullable|date',     // Validate only if present
            'email' => 'sometimes|required|string|email|max:255',
            'profile_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate only if present
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $user = auth()->user();
    
        // Update only the fields that are present in the request
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('birth_date')) {
            $user->birth_date = $request->birth_date;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->hasFile('profile_image')) {
            $imageName = time() . '.' . $request->profile_image->extension();
            $request->profile_image->move(public_path('images'), $imageName);
            $user->profile_image = 'images/' . $imageName;
        }
    
        $user->save();
    
        return response()->json([
            "status" => true,
            'message' => 'Profile updated successfully',
            'data' => $user, // Optionally return the updated user data
        ]);
    }
}

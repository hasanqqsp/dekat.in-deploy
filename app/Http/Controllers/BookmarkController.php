<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Display a listing of all bookmarks for the authenticated user.
     */
    /**
     * @OA\Get(
     *     path="/api/bookmarks",
     *     summary="Get all bookmarks for the authenticated user",
     *     tags={"Bookmark"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Bookmarks retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $bookmarks = Bookmark::with('location')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Bookmarks retrieved successfully',
            'data' => $bookmarks,
        ]);
    }

    /**
     * Store a newly created bookmark in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/bookmarks",
     *     summary="Create a new bookmark for the authenticated user",
     *     tags={"Bookmark"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"location_id"},
     *             @OA\Property(property="location_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bookmark created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bookmark already exists"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        // Check if the bookmark already exists
        $existingBookmark = Bookmark::where('user_id', $request->user()->id)
            ->where('location_id', $validated['location_id'])
            ->first();

        if ($existingBookmark) {
            return response()->json([
                'status' => false,
                'message' => 'Bookmark already exists',
            ], 400);
        }

        $bookmark = Bookmark::create([
            'user_id' => $request->user()->id,
            'location_id' => $validated['location_id'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Bookmark created successfully',
            'data' => $bookmark,
        ]);
    }

    /**
     * Display the specified bookmark.
     */
    public function show(Bookmark $bookmark)
    {
        // Ensure the bookmark belongs to the authenticated user
        if ($bookmark->user_id !== auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        return response()->json([
            'status' => true,
            'message' => 'Bookmark retrieved successfully',
            'data' => $bookmark->load('location'),
        ]);
    }

    /**
     * Remove the specified bookmark from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/bookmarks/{bookmark}",
     *     summary="Delete a bookmark by ID (must belong to authenticated user)",
     *     tags={"Bookmark"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="bookmark",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bookmark deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized access"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bookmark not found"
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        // Ensure the bookmark belongs to the authenticated user

        $bookmark = Bookmark::where('user_id', $request->user()->id)->where('location_id', $request->location_id)

            ->firstOrFail();

        if ($bookmark->user_id !== auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $bookmark->delete();

        return response()->json([
            'status' => true,
            'message' => 'Bookmark deleted successfully',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{userId}/bookmarks",
     *     summary="Get all bookmarks for a specific user",
     *     tags={"Bookmark"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bookmarks retrieved successfully for the user"
     *     )
     * )
     */
    public function getUserBookmarks($userId)
    {
        $bookmarks = Bookmark::with('location')
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Bookmarks retrieved successfully for the user',
            'data' => $bookmarks,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/location/{locationId}/bookmarks",
     *     summary="Get all bookmarks for a specific location",
     *     tags={"Bookmark"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="locationId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bookmarks retrieved successfully for the location"
     *     )
     * )
     */
    public function getLocationBookmarks($locationId)
    {
        $bookmarks = Bookmark::with('user')
            ->where('location_id', $locationId)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Bookmarks retrieved successfully for the location',
            'data' => $bookmarks,
        ]);
    }
}

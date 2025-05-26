<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Display a listing of all bookmarks for the authenticated user.
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
    public function destroy(Bookmark $bookmark)
    {
        // Ensure the bookmark belongs to the authenticated user
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
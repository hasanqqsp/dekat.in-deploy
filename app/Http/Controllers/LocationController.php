<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\Rating;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all locations
        $locations = Location::all();

        // Format the data as needed
        $formattedLocations = $locations->map(function ($location) {
            // Calculate the average rating for the location
            $averageRating = $location->ratings()->avg('rating');

            return [
                'id' => $location->id,
                'name' => $location->name,
                'category' => $location->category,
                'coords' => $location->coords,
                'images' => $location->image,
                'address' => $location->alamat_lengkap,
                'openHour' => $location->open_hour,
                'closeHour' => $location->close_hour,
                'startPrice' => $location->start_price,
                'endPrice' => $location->end_price,
                'averageRating' => round($averageRating, 1), // Round to 1 decimal place
            ];
        });

        // Return the formatted data as JSON
        return response()->json($formattedLocations, 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string',
            'category' => 'required|string',
            'coords' => 'required|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate multiple images
            'alamat_lengkap' => 'required|string',
            'open_hour' => 'required',
            'close_hour' => 'required',
            'start_price' => 'required|integer',
            'end_price' => 'required|integer',
        ]);

        // Handle multiple image uploads or set default images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $imagePaths[] = 'images/' . $imageName;
            }
        } else {
            // Set default images if no images are uploaded
            $imagePaths = [
                "/assets/content/prolog-kopi.jpg",
                "/assets/content/prolog-kopi.jpg",
                "/assets/content/prolog-kopi.jpg",
            ];
        }

        // Create the location
        $location = Location::create(array_merge($validated, [
            'image' => $imagePaths, // Save the array of image paths
            'contributor_id' => $request->user()->id ?? null, // Optional contributor ID
        ]));

        // Return a success response
        return response()->json([
            'status' => true,
            'message' => 'Location created successfully',
            'location' => $location,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        // Return the details of a single location
        return response()->json($location);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'category' => 'sometimes|required|string',
            'coords' => 'sometimes|required|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate multiple images
            'alamat_lengkap' => 'sometimes|required|string',
            'open_hour' => 'sometimes|required',
            'close_hour' => 'sometimes|required',
            'start_price' => 'sometimes|required|integer',
            'end_price' => 'sometimes|required|integer',
        ]);

        // Handle multiple image uploads or set default images
        $imagePaths = $location->image ?? []; // Keep existing images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $imagePaths[] = 'images/' . $imageName;
            }
        } elseif (empty($imagePaths)) {
            // Set default images if no images exist
            $imagePaths = [
                "/assets/content/prolog-kopi.jpg",
                "/assets/content/prolog-kopi.jpg",
                "/assets/content/prolog-kopi.jpg",
            ];
        }

        // Update the location
        $location->update(array_merge($validated, [
            'image' => $imagePaths, // Update the array of image paths
        ]));

        // Return a success response
        return response()->json([
            'status' => true,
            'message' => 'Location updated successfully',
            'location' => $location,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        // Delete the location
        $location->delete();

        // Return a success response
        return response()->json([
            'status' => true,
            'message' => 'Location deleted successfully',
        ]);
    }

    /**
     * Get all categories.
     */
    public function category()
    {
        $categoryOptions = ["Cafe", "Market", "Garden", "Farm", "Greenhouse"];

        return response()->json($categoryOptions, 200);
    }

    /**
     * Bookmark a location.
     */
    public function bookmark(Request $request, Location $location)
    {
        $user = $request->user();

        // Check if the location is already bookmarked
        if ($user->bookmarks()->where('location_id', $location->id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Location already bookmarked',
            ], 400);
        }

        // Create a new bookmark
        $user->bookmarks()->create(['location_id' => $location->id]);

        return response()->json([
            'status' => true,
            'message' => 'Location bookmarked successfully',
        ]);
    }

    /**
     * Get all bookmarks for the authenticated user.
     */
    public function getBookmarks(Request $request)
    {
        $user = $request->user();

        // Get all bookmarks for the user
        $bookmarks = $user->bookmarks()->with('location')->get();

        return response()->json($bookmarks);
    }

    public function getTotalRating(Request $request, $locationId)
    {
        $userId = $request->user()->id; // Get the authenticated user's ID

        $totalRating = Rating::where('user_id', $userId)
            ->where('location_id', $locationId)
            ->sum('rating');

        return response()->json([
            'status' => true,
            'totalRating' => $totalRating,
        ]);
    }

    public function getLocationReviews($locationId)
    {
        // Fetch all reviews for the given location
        $reviews = Rating::with('user') // Eager load user details
            ->where('location_id', $locationId)
            ->get();

        // Format the reviews as needed
        $formattedReviews = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'user_name' => $review->user->name, // Include the user's name
                'created_at' => $review->created_at,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Reviews retrieved successfully',
            'data' => $formattedReviews,
        ]);
    }
}

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
            return [
                'name' => $location->name,
                'category' => $location->category,
                'coords' => $location->coords,
                'image' => $location->image,
                'address' => $location->alamat_lengkap,
                'openHour' => $location->open_hour,
                'closeHour' => $location->close_hour,
                'startPrice' => $location->start_price,
                'endPrice' => $location->end_price,
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
            'image' => 'nullable|string',
            'alamat_lengkap' => 'required|string',
            'open_hour' => 'required',
            'close_hour' => 'required',
            'start_price' => 'required|integer',
            'end_price' => 'required|integer',
        ]);

        // Create the location
        $location = Location::create(array_merge($validated, [
            'contributor_id' => $request->user()->id,
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
            'image' => 'nullable|string',
            'alamat_lengkap' => 'sometimes|required|string',
            'open_hour' => 'sometimes|required',
            'close_hour' => 'sometimes|required',
            'start_price' => 'sometimes|required|integer',
            'end_price' => 'sometimes|required|integer',
        ]);

        // Update the location
        $location->update($validated);

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

    use App\Models\Rating;

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
}
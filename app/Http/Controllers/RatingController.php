<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Display a listing of all ratings.
     */
    public function index()
    {
        $ratings = Rating::with('user', 'location')->get();

        return response()->json([
            'status' => true,
            'message' => 'Ratings retrieved successfully',
            'data' => $ratings,
        ]);
    }

    /**
     * Store a newly created rating in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'location_id' => 'required|exists:locations,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $rating = Rating::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Rating created successfully',
            'data' => $rating,
        ]);
    }

    /**
     * Display the specified rating.
     */
    public function show(Rating $rating)
    {
        $rating->load('user', 'location');

        return response()->json([
            'status' => true,
            'message' => 'Rating retrieved successfully',
            'data' => $rating,
        ]);
    }

    /**
     * Update the specified rating in storage.
     */
    public function update(Request $request, Rating $rating)
    {
        $validated = $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $rating->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Rating updated successfully',
            'data' => $rating,
        ]);
    }

    /**
     * Remove the specified rating from storage.
     */
    public function destroy(Rating $rating)
    {
        $rating->delete();

        return response()->json([
            'status' => true,
            'message' => 'Rating deleted successfully',
        ]);
    }
}
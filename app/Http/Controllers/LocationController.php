<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function category()
    {
        $categoryOptions = ["Cafe", "Market", "Garden", "Farm", "Greenhouse"];
    
        return response()->json($categoryOptions, 200);
    }
    public function location()
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
                'address' => $location->alamat_lengkap, // Use 'alamat_lengkap' for address
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        //
    }
}

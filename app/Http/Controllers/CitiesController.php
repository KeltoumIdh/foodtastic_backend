<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CitiesController extends Controller
{
    public function index()
    {
        $cities = City::all();
        return response()->json( $cities, 200);
    }

    public function getCityById(int $id)
    {
        $city = City::findOrFail($id);

        return response()->json( $city, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $category = City::create($request->all());

        return response()->json(['category' => $category], 201);
    }

    // Get a specific category by ID
    public function show($id)
    {
        $category = City::findOrFail($id);
        return response()->json(['category' => $category], 200);
    }

    // Update a category
    public function update(Request $request, $id)
    {
        $category = City::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:categories,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $category->update($request->all());

        return response()->json(['category' => $category], 200);
    }

    // Delete a category
    public function delete($id)
    {
        $category = City::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'City deleted successfully'], 200);
    }
}
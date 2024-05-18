<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // Get all products
    public function index()
    {
        $products = Product::all();
        return response()->json(['products' => $products], 200);
    }

    // Create a new product
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity_available' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'producer_id' => 'required|exists:producers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $product = Product::create($request->all());

        return response()->json(['product' => $product], 201);
    }

    // Get a specific product by ID
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json(['product' => $product], 200);
    }

    // Update a product
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'quantity_available' => 'integer|min:0',
            'category_id' => 'exists:categories,id',
            'producer_id' => 'exists:producers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $product->update($request->all());

        return response()->json(['product' => $product], 200);
    }

    // Delete a product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // Get all products
    // public function index()
    // {
    //     $products = Product::all();
    //     return response()->json( $products, 200);
    // }
    public function index(Request $request)
    {
        $query = $request->input('query');
        $status = $request->input('status');

        $productsQuery = Product::query();

        if (!empty($query)) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            });
        }
        // Filter by status if provided
        if (!empty($status)) {
            $productsQuery->where('status', $status);
        }

        $products = $productsQuery->orderBy('created_at', 'desc')->paginate(50);

        if ($request->wantsJson()) {
            return response()->json($products);
        }

        // If no query or status, return all products
        if (empty($query) && empty($status)) {
            $allProducts = Product::paginate(10);
        }

        return response()->json($products ?? [], 200);
    }

    // Create a new product
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'price' => 'required|numeric|min:0',
    //         'quantity_available' => 'required|integer|min:0',
    //         'category_id' => 'required|exists:categories,id',
    //         'producer_id' => 'required|exists:producers,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 400);
    //     }

    //     $product = Product::create($request->all());

    //     return response()->json(['product' => $product], 201);
    // }
    public function store(Request $request)
{
    // Validate the incoming request data
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'categorie' => 'required|integer', // Assuming 'categorie' is the category ID sent from the frontend
        'producer' => 'required|integer',
        'quantity' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
        // 'image' => 'nullable|image|mimes:jpg,jpeg,png',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    $imagePath = null;
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $ext = $file->getClientOriginalExtension();
        $filename = time() . '.' . $ext;
        $file->move('images/products/', $filename);
        $imagePath = $filename;
    }


    $quantity = $request->quantity;

    // Determine the status based on quantity
    $low_stock_threshold = 0.1 * $quantity; // 10% of initial quantity
    if ($quantity == 0) {
        $status = 'Rupture de stock';
    } elseif ($quantity <= 10) {
        $status = 'Stock faible';
    } else {
        $status = 'Disponible';
    }

    // Create the product
    $product = Product::create([
        'name' => $request->name,
        'category_id' => $request->categorie,
        'producer_id' => $request->producer,
        'quantity' => $quantity,
        'price' => $request->price,
        'image_path' => $imagePath,
        'status' => $status,
        'initial_quantity' => $quantity, // Set initial_quantity to quantity
        'quantity_available' => $quantity,
        'quantity_sold' => 0,
    ]);

    return response()->json([
        'message' => 'Product created successfully!',
        'product' => $product
    ], 201);
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

        $product = Product::findOrFail($id);

        // Update other fields
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        $product->producer_id = $request->input('producer_id');
        $product->quantity_available = $request->input('quantity_available');
        $product->quantity_sold = $request->input('quantity_sold');

        // Update status based on quantity
        $quantity = $request->input('quantity_available');
        $low_stock_threshold = 0.1 * $product->initial_quantity; // 10% of initial quantity
        if ($quantity == 0) {
            $product->status = 'Rupture de stock';
        } elseif ($quantity <= 10) {
            $product->status = 'Stock faible';
        } else {
            $product->status = 'Disponible';
        }


        // Save the product instance
        $product->save();
        return response()->json(['product' => $product], 200);
    }

    public function edit($id)
    {

        $product = product::findOrFail($id);
        return response()->json($product ?? [], 200);
    }
    // Delete a product
    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
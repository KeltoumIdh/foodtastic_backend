<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // Get all orders
    public function index()
    {
        $orders = Order::all();
        return response()->json(['orders' => $orders], 200);
    }

    // Create a new order
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $order = Order::create($request->all());

        return response()->json(['order' => $order], 201);
    }

    // Get a specific order by ID
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json(['order' => $order], 200);
    }

    // Update an order
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id' => 'exists:users,id',
            'total_amount' => 'numeric|min:0',
            'status' => 'in:pending,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $order->update($request->all());

        return response()->json(['order' => $order], 200);
    }

    // Delete an order
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully'], 200);
    }
}
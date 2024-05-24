<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

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

    // new order
    public function multipleOrders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'total_ammount' => 'required|integer',
            'auth_user' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $order = Order::create([
            "user_id" => $request->auth_user,
            "total_amount" => $request->total_ammount,
        ]);


        $newOrders = [];


        foreach ($request->products as $product) {
            $OrderItem = OrderItem::create([
                "order_id" => $order['id'],
                "product_id" => $product['id'],
                "quantity" => $product['quantity'],
                "price" => $product['price'],
            ]);

            // Get product
            $currentProduct = Product::where('id', $product['id'])->first();
            $currentProduct->quantity_available = $currentProduct->quantity_available - $product['quantity'];
            $currentProduct->quantity_sold = $currentProduct->quantity_sold + $product['quantity'];
            $currentProduct->save();

            $newOrders[] = $OrderItem;
        }


        $data = [
            'title' => 'Page Title Here....',
            'date' => date('m/d/Y'),
            'order' => $order,
            'products' => $newOrders
        ];

        $dompdf = new Dompdf();

        // Load HTML content from a blade view
        $html = view('pdf.view', $data)->render();

        // Set options (optional)
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        // Apply the options
        $dompdf->setOptions($options);

        // Load HTML into Dompdf
        $dompdf->loadHtml($html);

        // Render the PDF
        $dompdf->render();

        // Generate a unique file name for the PDF
        $filename = 'assets/uploads/pdf/' . 'document_' . time() . '.pdf';
        $name = 'document_' . time() . '.pdf';
        // Save the PDF file to the public directory
        $path = public_path($filename);
        file_put_contents($path, $dompdf->output());


        // Return a response with a download link
        return response()->json(["file_path" => $filename], 200);
    }
}
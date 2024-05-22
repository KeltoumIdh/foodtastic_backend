<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    // Get all admins
    public function index()
    {
        $admins = User::where('role','admin')->get();
        return response()->json( $admins, 200);
    }

    // Create a new admin
    public function store(Request $request)
{
    // Validate the request
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:admins,email',
        'password' => 'required|string|min:6',
        'role' => 'nullable|string|in:admin,owner', // Adjust the validation rule as needed
    ]);

    try {
        // Create the admin user
        $admin = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'], // Assign the provided role
        ]);

        return response()->json(['admin' => $admin], 201);
    } catch (\Exception $e) {
        // Log the error for further investigation
        Log::error('Failed to create admin user', ['error' => $e->getMessage()]);

        // Return a generic error response
        return response()->json(['message' => 'Internal Server Error'], 500);
    }
}




    /**
     * update auth admin info
     * @param Request $request
     * @param string $id
     */
    public function update(string $id, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'adress' => 'nullable|string|max:255',
        ]);


        // Get admin info
        $adminData = User::findOrFail($id);


        // Save Data
        $adminData->name = $request->name;
        $adminData->email = $request->email;
        $adminData->address = $request->adress;
        $adminData->save();


        // return success message
        return response()->json(['ok' => true, 'message' => 'updated'], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return response()->json(Auth::admin(), 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    // Get a specific admin by ID
    public function show($id)
    {
        $admin = User::findOrFail($id);
        return response()->json( $admin, 200);
    }

    // // Update a admin
    // public function update(Request $request, $id)
    // {
    //     $admin = User::findOrFail($id);

    //     $request->validate([
    //         'name' => 'string|max:255',
    //         'email' => 'email|unique:admins,email,' . $admin->id,
    //         'password' => 'string|min:6',
    //         'address' => 'nullable|string|max:255',
    //         'role' => 'nullable|in:admin,admin',
    //     ]);

    //     $admin->update($request->all());

    //     return response()->json(['admin' => $admin], 200);
    // }

    // Delete a admin
    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
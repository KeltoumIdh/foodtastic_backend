<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // Get all users
    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users], 200);
    }

    // Create a new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'address' => 'nullable|string|max:255',
            'role' => 'nullable|in:admin,user',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'role' => $request->role ?? 'user',
        ]);

        return response()->json(['user' => $user], 201);
    }

    // Get a specific user by ID
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['user' => $user], 200);
    }

    // Update a user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'string|min:6',
            'address' => 'nullable|string|max:255',
            'role' => 'nullable|in:admin,user',
        ]);

        $user->update($request->all());

        return response()->json(['user' => $user], 200);
    }

    // Delete a user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
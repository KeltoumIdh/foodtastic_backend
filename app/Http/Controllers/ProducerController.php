<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use Illuminate\Http\Request;

class ProducerController extends Controller
{
    public function index()
    {
        return Producer::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'contact_info' => 'required|string|max:255',
        'city_id' => 'required|integer|exists:cities,id',
    ]);


    $producer = new Producer();
    $producer->name = $validatedData['name'];
    $producer->contact_info = $validatedData['contact_info'];
    $producer->city_id = $validatedData['city_id'];
    $producer->save();

    return response()->json(['message' => 'Producer created successfully!'], 201);
}


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Producer  $producer
     * @return \Illuminate\Http\Response
     */
    public function show(Producer $producer)
    {
        return $producer;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Producer  $producer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Producer $producer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'required|string|max:255',
            'city_id' => 'required|integer|exists:cities,id',
        ]);

        $producer->update($validated);

        return response()->json($producer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Producer  $producer
     * @return \Illuminate\Http\Response
     */
    public function delete(int $id)
    {
        $producer = Producer::findOrFail($id);
        $producer->delete();

        return response()->json('deleted!', 204);
    }
}
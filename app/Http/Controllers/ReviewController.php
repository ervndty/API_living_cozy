<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{

    public function index(Request $request)
    {
        $product_id = $request->query('product_id');

        if (!$product_id) {
            return response()->json(['error' => 'Product ID is required'], 400);
        }

        // Memuat ulasan dengan relasi pengguna
        $reviews = Review::with('user')->where('product_id', $product_id)->get();

        return response()->json($reviews);
    }

    

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'user_id' => 'required|exists:users,user_id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'photo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo_path')) {
            $path = $request->file('photo_path')->store('public/photos');
            $data['photo_path'] = $path;
        }

        $review = Review::create($data);

        return response()->json($review, 201);
    }

    public function show($id)
    {
        $review = Review::findOrFail($id);

        return response()->json($review);
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'user_id' => 'required|exists:users,user_id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'photo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo_path')) {
            if ($review->photo_path) {
                Storage::delete($review->photo_path);
            }

            $path = $request->file('photo_path')->store('public/photos');
            $data['photo_path'] = $path;
        }

        $review->update($data);

        return response()->json($review);
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if ($review->photo_path) {
            Storage::delete($review->photo_path);
        }

        $review->delete();

        return response()->json(null, 204);
    }
}

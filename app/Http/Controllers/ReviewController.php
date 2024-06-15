<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return response()->json(Review::all(), 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'user_id' => 'required|exists:users,user_id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create($validatedData);
        return response()->json($review, 201);
    }

    public function show($id)
    {
        $review = Review::find($id);

        if ($review) {
            return response()->json($review, 200);
        } else {
            return response()->json(['message' => 'Review not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'user_id' => 'required|exists:users,user_id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::find($id);

        if ($review) {
            $review->update($validatedData);
            return response()->json($review, 200);
        } else {
            return response()->json(['message' => 'Review not found'], 404);
        }
    }

    public function destroy($id)
    {
        $review = Review::find($id);

        if ($review) {
            $review->delete();
            return response()->json(['message' => 'Review deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Review not found'], 404);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\WishlistItem;
use Illuminate\Http\Request;

class WishlistItemController extends Controller
{
    public function index()
    {
        return response()->json(WishlistItem::all(), 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'wishlist_id' => 'required|exists:wishlists,wishlist_id',
            'product_id' => 'required|exists:products,product_id',
        ]);

        $wishlistItem = WishlistItem::create($validatedData);
        return response()->json($wishlistItem, 201);
    }

    public function show($id)
    {
        $wishlistItem = WishlistItem::find($id);

        if ($wishlistItem) {
            return response()->json($wishlistItem, 200);
        } else {
            return response()->json(['message' => 'Wishlist item not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'wishlist_id' => 'required|exists:wishlists,wishlist_id',
            'product_id' => 'required|exists:products,product_id',
        ]);

        $wishlistItem = WishlistItem::find($id);

        if ($wishlistItem) {
            $wishlistItem->update($validatedData);
            return response()->json($wishlistItem, 200);
        } else {
            return response()->json(['message' => 'Wishlist item not found'], 404);
        }
    }

    public function destroy($id)
    {
        $wishlistItem = WishlistItem::find($id);

        if ($wishlistItem) {
            $wishlistItem->delete();
            return response()->json(['message' => 'Wishlist item deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Wishlist item not found'], 404);
        }
    }
}

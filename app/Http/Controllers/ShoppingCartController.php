<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShoppingCart;

class ShoppingCartController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user(); // Assuming token-based authentication

        // Eager load the related product data for the authenticated user
        $shoppingCarts = ShoppingCart::with('product')
                                     ->where('user_id', $user->user_id)
                                     ->get();

        return response()->json(['shopping_carts' => $shoppingCarts]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
        ]);

        $shoppingCart = ShoppingCart::where('user_id', $request->user()->user_id)
                                    ->where('product_id', $request->product_id)
                                    ->first();

        if ($shoppingCart) {
            $shoppingCart->quantity += $request->quantity;
            $shoppingCart->save();
        } else {
            $shoppingCart = ShoppingCart::create([
                'user_id' => $request->user()->user_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['shopping_cart' => $shoppingCart], 201);
    }

    public function update(Request $request, $product_id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $shoppingCart = ShoppingCart::where('user_id', $request->user()->user_id)
                                    ->where('product_id', $product_id)
                                    ->firstOrFail();

        $shoppingCart->quantity = $request->quantity;
        $shoppingCart->save();

        return response()->json(['shopping_cart' => $shoppingCart], 200);
    }

    public function destroy(Request $request, $cart_id)
{
    $shoppingCart = ShoppingCart::where('user_id', $request->user()->user_id)
                                ->where('cart_id', $cart_id)
                                ->firstOrFail();

    $shoppingCart->delete();

    return response()->json(null, 204);
}
}

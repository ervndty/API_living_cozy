<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShoppingCart;

class ShoppingCartController extends Controller
{
    public function index()
    {
        $shoppingCarts = ShoppingCart::all();

        return response()->json(['shopping_carts' => $shoppingCarts]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $shoppingCart = ShoppingCart::create([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        return response()->json(['shopping_cart' => $shoppingCart], 201);
    }

    public function show($id)
    {
        $shoppingCart = ShoppingCart::findOrFail($id);

        return response()->json(['shopping_cart' => $shoppingCart]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $shoppingCart = ShoppingCart::findOrFail($id);
        $shoppingCart->user_id = $request->user_id;
        $shoppingCart->product_id = $request->product_id;
        $shoppingCart->quantity = $request->quantity;
        $shoppingCart->save();

        return response()->json(['shopping_cart' => $shoppingCart], 200);
    }

    public function destroy($id)
    {
        $shoppingCart = ShoppingCart::findOrFail($id);
        $shoppingCart->delete();

        return response()->json(null, 204);
    }
}

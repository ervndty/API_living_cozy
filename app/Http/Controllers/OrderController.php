<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
    // Mendapatkan user yang sedang login
        $user = $request->user();

    // Menampilkan semua orders milik user yang sedang login
        $orders = Order::where('user_id', $user->user_id)->get();
    
        return response()->json(['orders' => $orders], 200);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id', 
            'product_id' => 'required|exists:products,product_id', 
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Membuat order baru
        $order = Order::create($request->all());
        return response()->json(['order' => $order], 201);
    }

    public function show($id)
    {
        // Menampilkan detail order berdasarkan ID
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return response()->json(['order' => $order], 200);
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'user_id' => 'exists:users,user_id', 
            'product_id' => 'exists:products,product_id', 
            'quantity' => 'integer|min:1',
            'price' => 'numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Mengupdate order berdasarkan ID
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->update($request->all());
        return response()->json(['order' => $order], 200);
    }

    public function destroy($id)
    {
        // Menghapus order berdasarkan ID
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully'], 200);
    }
}

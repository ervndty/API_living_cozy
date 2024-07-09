<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Midtrans\Config;
use App\Models\User;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function getTranscationCount()
    {
        $count = Payment::count();
        return response()->json(['jumlah_transaction' => $count]);
    }

    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function create(Request $request)
{
    $user = User::findOrFail($request->user_id);
    $cartItems = ShoppingCart::where('user_id', $request->user_id)->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['error' => 'Shopping cart is empty.'], 404);
    }

    $order_id = (string) Str::uuid();
    $item_details = [];
    $gross_amount = 0;

    foreach ($cartItems as $item) {
        $product = Product::findOrFail($item->product_id);
        $item_details[] = [
            'id' => $product->product_id,
            'price' => $product->price,
            'quantity' => $item->quantity,
            'name' => $product->product_name,
        ];
        $gross_amount += $product->price * $item->quantity;
    }

    $params = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => $gross_amount,
        ],
        'item_details' => $item_details,
        'customer_details' => [
            'first_name' => $user->name,
            'email' => $user->email,
        ],
    ];

    try {
        $auth = base64_encode(config('midtrans.server_key') . ':');
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Basic $auth",
        ])->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $params);

        $response = json_decode($response->body());

        if (isset($response->token)) {
            $payment = new Payment;
            $payment->order_id = $order_id;
            $payment->user_id = $user->user_id;
            $payment->status = 'pending';
            $payment->price = $gross_amount;
            $payment->customer_first_name = $user->name;
            $payment->customer_email = $user->email;
            $payment->item_name = json_encode($item_details);
            $payment->checkout_link = $response->redirect_url;
            $payment->save();

            return response()->json(['token' => $response->token, 'redirect_url' => $response->redirect_url]);
        } else {
            return response()->json(['error' => 'Token not found in the response.'], 500);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    public function webhook(Request $request)
    {
        $auth = base64_encode(config('midtrans.server_key'));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Basic $auth",
        ])->get("https://api.sandbox.midtrans.com/v2/$request->order_id/status");

        $response = json_decode($response->body());

        $payment = Payment::where('order_id', $response->order_id)->firstOrFail();

        if (in_array($payment->status, ['settlement', 'capture'])) {
            return response()->json('Payment has already been processed');
        }

        switch ($response->transaction_status) {
            case 'capture':
            case 'settlement':
                $payment->status = $response->transaction_status;

                // Hapus item dari keranjang
                $products = json_decode($payment->item_name, true);
                foreach ($products as $product) {
                    ShoppingCart::where('user_id', $payment->user_id)
                                ->where('product_id', $product['id'])
                                ->delete();
                }
                break;
            case 'deny':
                $payment->status = 'deny';
                break;
            case 'expire':
                $payment->status = 'expire';
                break;
            case 'cancel':
                $payment->status = 'cancel';
                break;
        }

        $payment->save();

        return response()->json('success');
    }

    public function history(Request $request)
{
    $user_id = $request->input('user_id');

    if (!$user_id) {
        return response()->json(['error' => 'User ID is required'], 400);
    }

    $payments = Payment::where('user_id', $user_id)->get();

    foreach ($payments as $payment) {
        $items = json_decode($payment->item_name, true);
        $payment->items = $items;
    }

    return response()->json($payments);
}


    public function approveTransaction($id)
    {
        $transaction = Payment::find($id);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $transaction->status = 'success';
        $transaction->save();

        return response()->json(['message' => 'Transaction approved successfully'], 200);
    }

    public function getAllTransactions()
    {
        $payments = Payment::all();

        foreach ($payments as $payment) {
            $items = json_decode($payment->item_name, true);
            $payment->items = $items;
        }

        return response()->json($payments);
    }

    public function destroy($id)
    {
        $transaction = Payment::find($id);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Hapus transaksi
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully.'], 200);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Midtrans\Config;
use Illuminate\Support\Str;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function create(Request $request)
    {
        // Validasi input request (Anda bisa aktifkan jika diperlukan)
        // $request->validate([
        //     'user_id' => 'required|exists:users,id',
        //     'order_id' => 'required|exists:orders,id',
        //     'amount' => 'required|numeric',
        //     'payment_method' => 'required|string|max:50'
        // ]);

        // Buat record payment di database
        // $payment = Payment::create([
        //     'user_id' => $request->user_id,
        //     'order_id' => $request->order_id,
        //     'amount' => $request->amount,
        //     'payment_method' => $request->payment_method,
        //     'status' => 'pending'
        // ]);

        // Buat parameter untuk transaksi Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => Str::uuid(),
                'gross_amount' => $request->price,
            ],
            'item_details' => [
                [
                    'price' => $request->price,
                    'quantity' => 1,
                    'name' => $request->item_name,
                ]
            ],
            'customer_details' => [
                'first_name' => $request->customer_first_name,
                'email' => $request->customer_email,
            ],
        ];

        try {
            $auth = base64_encode(config('midtrans.server_key'));
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic $auth",
            ])->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $params);

            $response = json_decode($response->body());

            if (isset($response->redirect_url)) {
                $payment = new Payment;
                $payment->order_id = $params['transaction_details']['order_id'];
                $payment->status = 'pending';
                $payment->price = $request->price;
                $payment->customer_first_name = $request->customer_first_name;
                $payment->customer_email = $request->customer_email;
                $payment->item_name = $request->item_name;
                $payment->checkout_link = $response->redirect_url;
                $payment->save();

                return response()->json(['redirect_url' => $response->redirect_url]);
            } else {
                return response()->json(['error' => 'Redirect URL not found in the response.'], 500);
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
                $payment->status = 'capture';
                break;
            case 'settlement':
                $payment->status = 'settlement';
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
}

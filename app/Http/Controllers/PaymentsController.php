<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentsController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        if (!Config::$isProduction) {
            Config::$curlOptions = [
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
            ];
        }
    }

    public function create(Request $request)
    {
        // Validate request input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,user_id', // Adjusted validation rule
            'order_id' => 'required|integer|exists:orders,order_id', // Adjusted validation rule
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Extract validated data
        $validated = $validator->validated();
        $userID = $validated['user_id'];
        $orderID = $validated['order_id'];
        $amount = $validated['amount'];

        // Retrieve related user and order data
        $user = User::findOrFail($userID);
        $order = Order::with('product')->findOrFail($orderID);

        // Prepare parameters for Midtrans API
        $params = [
            'transaction_details' => [
                'order_id' => uniqid(),
                'gross_amount' => $amount,
            ],
            'item_details' => [
                [
                    'id' => $order->product_id,
                    'price' => $order->price,
                    'quantity' => $order->quantity,
                    'name' => $order->product->name,
                ],
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email, // Ensure email is available
            ],
        ];

        try {
            // Log request parameters for debugging
            Log::info('Sending request to Midtrans', ['params' => $params]);

            // Get Snap token and redirect URL from Midtrans
            $snapToken = Snap::getSnapToken($params);
            $transaction = Snap::createTransaction($params);

            // Save payment data to the database
            $payment = new Payment();
            $payment->user_id = $userID;
            $payment->order_id = $orderID;
            $payment->amount = $amount;
            $payment->status = 'pending';
            $payment->checkout_link = $transaction->redirect_url;
            $payment->save();

            return response()->json(['snap_token' => $snapToken, 'redirect_url' => $transaction->redirect_url]);
        } catch (\Exception $e) {
            Log::error('Error creating payment', [
                'message' => $e->getMessage(),
                'params' => $params,
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

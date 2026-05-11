<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MpesaService;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaPaymentController extends Controller
{
    public function stkPush(Request $request)
    {
        $phone = $request->phone ?? '254708374149';
        $amount = $request->amount ?? 1;

        $timestamp = now()->format('YmdHis');
        $shortcode = env('MPESA_SHORTCODE', '174379');
        $passkey = env('MPESA_PASSKEY');
        $password = base64_encode($shortcode . $passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => env('MPESA_CALLBACK_URL'), // use env, not route()
            'AccountReference' => 'TEST123',
            'TransactionDesc' => 'Test M-Pesa payment',
        ];

        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withToken($accessToken)
                ->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', $payload);

            Log::info('📤 M-Pesa STK Request:', $payload);
            Log::info('📥 M-Pesa STK Response:', $response->json());

            if ($response->failed() || isset($response['errorCode'])) {
                Log::error('❌ STK Push failed', ['response' => $response->json()]);
                return response()->json([
                    'success' => false,
                    'message' => 'M-Pesa STK push failed.',
                    'response' => $response->json()
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'STK push sent',
                'response' => $response->json()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ M-Pesa STK Exception: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'M-Pesa request failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function getAccessToken()
    {
        $consumerKey = env('MPESA_CONSUMER_KEY');
        $consumerSecret = env('MPESA_CONSUMER_SECRET');

        $response = Http::withBasicAuth($consumerKey, $consumerSecret)
            ->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

        if ($response->failed()) {
            Log::error('❌ Failed to get M-Pesa access token', ['response' => $response->body()]);
            throw new \Exception('Unable to fetch access token');
        }

        return $response['access_token'];
    }

    public function initiate(Request $request, MpesaService $mpesa)
    {
        $request->validate([
            'phone' => 'required',
            'amount' => 'required|numeric',
            'sale_id' => 'required|exists:sales,id',
        ]);

        $desc = "Payment for Sale #" . $request->sale_id;
        $ref = 'SALE' . $request->sale_id;

        $response = $mpesa->stkPush($request->phone, $request->amount, $ref, $desc);

        Payment::create([
            'sale_id' => $request->sale_id,
            'phone' => $request->phone,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        return response()->json($response);
    }

    public function callback(Request $request)
    {
        Log::info('📞 M-Pesa Callback Raw:', $request->all());

        $payload = json_decode($request->getContent(), true);
        $stkCallback = $payload['Body']['stkCallback'] ?? null;

        if ($stkCallback && $stkCallback['ResultCode'] === 0) {
            $metadata = collect($stkCallback['CallbackMetadata']['Item'])
                ->pluck('Value', 'Name');

            Payment::whereNull('mpesa_code')
                ->where('phone', $metadata['PhoneNumber'] ?? null)
                ->where('amount', $metadata['Amount'] ?? null)
                ->latest()
                ->first()?->update([
                    'mpesa_code' => $metadata['MpesaReceiptNumber'] ?? null,
                    'status' => 'confirmed',
                ]);

            Log::info('✅ Payment confirmed:', $metadata->toArray());

        } else {
            $reason = $stkCallback['ResultDesc'] ?? 'Unknown';
            Log::warning('⚠️ STK Push not successful.', [
                'ResultCode' => $stkCallback['ResultCode'] ?? null,
                'ResultDesc' => $reason,
                'Request' => $payload
            ]);
        }

        return response()->json(['message' => 'Callback processed']);
    }
}

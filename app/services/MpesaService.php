<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class MpesaService
{
    public function getAccessToken()
    {
        $response = Http::withBasicAuth(
            config('mpesa.consumer_key'),
            config('mpesa.consumer_secret')
        )->get($this->baseUrl() . '/oauth/v1/generate?grant_type=client_credentials');

        return $response['access_token'];
    }

    public function stkPush($phone, $amount, $accountRef, $desc)
    {
        $timestamp = now()->format('YmdHis');
        $password = base64_encode(config('mpesa.shortcode') . config('mpesa.passkey') . $timestamp);

        $payload = [
            'BusinessShortCode' => config('mpesa.shortcode'),
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => config('mpesa.shortcode'),
            'PhoneNumber' => $phone,
            'CallBackURL' => config('mpesa.callback_url'),
            'AccountReference' => $accountRef,
            'TransactionDesc' => $desc,
        ];

        return Http::withToken($this->getAccessToken())
            ->post($this->baseUrl() . '/mpesa/stkpush/v1/processrequest', $payload)
            ->json();
    }

    private function baseUrl()
    {
        return config('mpesa.sandbox') ? 'https://sandbox.safaricom.co.ke' : 'https://api.safaricom.co.ke';
    }
}

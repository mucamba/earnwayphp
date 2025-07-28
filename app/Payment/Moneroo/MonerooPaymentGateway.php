<?php

namespace App\Payment\Moneroo;

use App\Payment\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MonerooPaymentGateway implements PaymentGateway
{
    private $credentials;

    public function __construct()
    {
        $this->credentials = \App\Models\PaymentGateway::getCredentials('moneroo');

        if (empty($this->credentials['api_key']) || empty($this->credentials['api_secret'])) {
            throw new \Exception('Moneroo API credentials are not configured.');
        }
    }

    public function deposit($amount, $currency, $trxId)
    {
        $apiKey = $this->credentials['api_secret'];
        $url    = 'https://api.moneroo.io/v1/payments/initialize';

        $data = [
            'amount'       => $amount,
            'currency'     => $currency,
            'description'  => 'Deposit for transaction '.$trxId,
            'reference'    => $trxId,
            'callback_url' => route('payment.ipn', ['gateway' => 'moneroo']),
            'success_url'  => route('user.deposit.success'),
            'cancel_url'   => route('user.deposit.cancel'),
        ];

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$apiKey,
            'Accept: application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response  = curl_exec($ch);
        $httpcode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpcode != 201) {
            Log::error('Moneroo payment init error', ['httpcode' => $httpcode, 'error' => $curlError, 'response' => $response]);

            return back()->withErrors(['error' => 'Moneroo payment failed to initialize.']);
        }

        $response_data = json_decode($response, true);
        if (empty($response_data['checkout_url'])) {
            Log::error('Moneroo payment missing checkout_url', ['response' => $response_data]);

            return back()->withErrors(['error' => 'Moneroo checkout URL missing.']);
        }

        return redirect($response_data['checkout_url']);
    }

    public function handleIPN(Request $request)
    {
        $secret    = $this->credentials['webhook_signing_secret'];
        $payload   = $request->getContent();
        $signature = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($signature, $request->header('X-Moneroo-Signature'))) {
            abort(403, 'Invalid IPN signature');
        }

        $data   = json_decode($payload, true);
        $status = $data['status']    ?? null;
        $trxId  = $data['reference'] ?? null;

        if ($status === 'success') {
            \Transaction::completeTransaction($trxId);
        } else {
            \Transaction::failTransaction($trxId);
        }

        return response()->json(['status' => $status]);
    }
}

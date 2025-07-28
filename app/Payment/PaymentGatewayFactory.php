<?php

namespace App\Payment;

use App\Payment\Coinbase\CoinbasePaymentGateway;
use App\Payment\Cryptomus\CryptomusPaymentGateway;
use App\Payment\Flutterwave\FlutterwavePaymentGateway;
use App\Payment\Mollie\MolliePaymentGateway;
use App\Payment\Moneroo\MonerooPaymentGateway;
use App\Payment\Paypal\PaypalPaymentGateway;
use App\Payment\Paystack\PaystackPaymentGateway;
use App\Payment\Stripe\StripePaymentGateway;
use Exception;
use Illuminate\Support\Facades\App;

class PaymentGatewayFactory
{
    /**
     * Create an instance of a payment gateway.
     *
     *
     * @throws Exception
     */
    public function getGateway(string $gatewayCode)
    {
        return match ($gatewayCode) {
            'paypal'      => App::make(PaypalPaymentGateway::class),
            'stripe'      => App::make(StripePaymentGateway::class),
            'mollie'      => App::make(MolliePaymentGateway::class),
            'coinbase'    => App::make(CoinbasePaymentGateway::class),
            'paystack'    => App::make(PaystackPaymentGateway::class),
            'flutterwave' => App::make(FlutterwavePaymentGateway::class),
            'cryptomus'   => App::make(CryptomusPaymentGateway::class),
            'manual'      => App::make(ManualPaymentSystem::class),
            'moneroo'     => App::make(MonerooPaymentGateway::class),
            default       => throw new Exception(sprintf('Unsupported payment gateway: %s', $gatewayCode)),
        };
    }
}

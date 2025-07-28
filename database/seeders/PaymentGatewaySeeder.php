<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Seed the payment_gateways table with unique gateways.
     */
    public function run(): void
    {
        $gateways = [
            [
                'code'        => 'moneroo',
                'logo'        => 'general/static/gateway/moneroo.svg',
                'name'        => 'Moneroo',
                'currencies'  => json_encode(['USD', 'EUR', 'GBP', 'BTC', 'ETH']),
                'credentials' => json_encode([
                    'api_key'                => 'api_key',
                    'api_secret'             => 'api_secret',
                    'webhook_signing_secret' => 'webhook_signing_secret',
                ]),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => 1,
            ],
            [
                'code'        => 'strowallet',
                'logo'        => 'general/static/gateway/strowallet.png',
                'name'        => 'Strowallet',
                'currencies'  => json_encode(['USD', 'NGN']),
                'credentials' => json_encode([
                    'public_key' => 'public_key',
                    'secret_key' => 'secret_key',
                    'mode'       => 'sandbox',
                ]),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'        => 'binance',
                'logo'        => 'general/static/gateway/binance.png',
                'name'        => 'Binance',
                'currencies'  => json_encode(['USD', 'EUR', 'GBP', 'BTC', 'ETH', 'USDT', 'BNB']),
                'credentials' => json_encode([
                    'api_key'    => 'api_key',
                    'api_secret' => 'api_secret',
                ]),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'airtel',
                'logo'           => 'general/static/gateway/airtel.png',
                'name'           => 'Airtel',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP']),
                'credentials'    => json_encode(['api_key' => 'api_key']),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'blockchain',
                'logo'           => 'general/static/gateway/blockchain.png',
                'name'           => 'Blockchain',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP']),
                'credentials'    => json_encode(['api_key' => 'api_key']),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'blockio',
                'logo'           => 'general/static/gateway/blockio.png',
                'name'           => 'Block.io',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP']),
                'credentials'    => json_encode(['api_key' => 'api_key']),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'bitpayserver',
                'logo'           => 'general/static/gateway/btcpayserver.png',
                'name'           => 'Bitpayserver',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP']),
                'credentials'    => json_encode(['api_key' => 'api_key']),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'cashmaal',
                'logo'           => 'general/static/gateway/cashmaal.png',
                'name'           => 'Cashmaal',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP']),
                'credentials'    => json_encode(['api_key' => 'api_key']),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'coingate',
                'logo'           => 'general/static/gateway/coingate.png',
                'name'           => 'Coingate',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP']),
                'credentials'    => json_encode(['api_key' => 'api_key']),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'coinpayments',
                'logo'           => 'general/static/gateway/coinpayments.svg',
                'name'           => 'Coinpayments',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP']),
                'credentials'    => json_encode(['api_key' => 'api_key']),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'instamojo',
                'logo'           => 'general/static/gateway/instamojo.png',
                'name'           => 'Instamojo',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP']),
                'credentials'    => json_encode(['api_key' => 'api_key']),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'mtn',
                'logo'           => 'general/static/gateway/mtn.png',
                'name'           => 'MTN',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP']),
                'credentials'    => json_encode(['api_key' => 'api_key']),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'nowpayments',
                'logo'           => 'general/static/gateway/nowpayments.png',
                'name'           => 'Nowpayments',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP']),
                'credentials'    => json_encode(['api_key' => 'api_key']),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'razorpay',
                'logo'           => 'general/static/gateway/razorpay.png',
                'name'           => 'Razorpay',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP']),
                'credentials'    => json_encode(['api_key' => 'api_key']),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'twocheckout',
                'logo'           => 'general/static/gateway/twocheckout.png',
                'name'           => 'Twocheckout',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP']),
                'credentials'    => json_encode(['api_key' => 'api_key']),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'voguepay',
                'logo'           => 'general/static/gateway/voguepay.png',
                'name'           => 'Voguepay',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP']),
                'credentials'    => json_encode(['api_key' => 'api_key']),
                'withdraw_field' => null,
                'ipn'            => true,
                'status'         => true,
            ],
        ];

        foreach ($gateways as $gateway) {
            $attributes = ['code' => $gateway['code']];
            $values     = $gateway;
            unset($values['code']);
            DB::table('payment_gateways')->updateOrInsert($attributes, $values);
        }
    }
}

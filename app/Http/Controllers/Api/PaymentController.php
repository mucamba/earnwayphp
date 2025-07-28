<?php

namespace App\Http\Controllers\Api;

use App\Data\TransactionData;
use App\Enums\AmountFlow;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Merchant\PaymentInitiateRequest;
use Currency;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Transaction;
use Wallet;

class PaymentController extends Controller
{
    public function initiatePayment(PaymentInitiateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $merchant  = $request->merchant; // Ensure merchant is being provided (e.g., via middleware or route model binding)

        // Validate currency existence and consistency.
        if (! Currency::exists($validated['currency_code'])) {
            return response()->json(['error' => 'Invalid currency code.'], 422);
        }

        // Ensures that the provided currency code exists.
        if ($merchant->currency->code !== $validated['currency_code']) {
            return response()->json(['error' => 'Currency code mismatch.'], 422);
        }

        // Prepare payment data
        $paymentData = array_merge(
            $request->only([
                'ref_trx',
                'description',
                'ipn_url',
                'cancel_redirect',
                'success_redirect',
                'customer_name',
                'customer_email',
            ]),
            [
                'merchant_id'   => $merchant->id,
                'merchant_name' => $merchant->business_name,
                'amount'        => $validated['payment_amount'],
                'currency_code' => $validated['currency_code'],
            ]
        );

        // Calculate fee and net amounts.
        $calculation = $this->calculatePaymentAmounts((float) $validated['payment_amount'], $merchant->fee);

        try {
            DB::beginTransaction();

            $merchantWallet = Wallet::getWalletByUserId($merchant->user->id, $merchant->currency->code);

            $transaction = Transaction::create(new TransactionData(
                user_id: $merchant->user->id,
                trx_type: TrxType::RECEIVE_PAYMENT,
                amount: $calculation['amount'],
                amount_flow: AmountFlow::PLUS,
                fee: $calculation['fee'],
                currency: $merchant->currency->code,
                net_amount: $calculation['net_amount'],
                payable_amount: $validated['payment_amount'],
                payable_currency: $validated['currency_code'],
                wallet_reference: $merchantWallet->uuid,
                trx_data: $paymentData,
                description: $paymentData['description'] ?? __('Payment from :customer', ['customer' => $paymentData['customer_name']]),
                status: TrxStatus::PENDING
            ));

            DB::commit();

            // Encrypt transaction ID
            $encryptedTrxId = Crypt::encryptString($transaction->trx_id);

            // Generate Laravel Signed URL with token (No custom hash needed)
            $paymentUrl = URL::signedRoute('payment.checkout', [
                'token' => $encryptedTrxId,
            ], now()->addMinutes(900)); // URL expires in 900 minutes

            return response()->json([
                'payment_url' => $paymentUrl,
                'info'        => $paymentData,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            // Optionally log the exception here before returning a response.
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Calculates the payment amounts including fee deduction.
     */
    protected function calculatePaymentAmounts(float $amount, float $merchantFee): array
    {
        $fee       = $amount * $merchantFee / 100;
        $netAmount = $amount - $fee;

        return [
            'fee'        => $fee,
            'amount'     => $netAmount,
            'net_amount' => $netAmount,
        ];
    }
}

<?php

namespace App\Services\Handlers;

use App\Enums\AmountFlow;
use App\Models\Transaction;
use App\Services\Handlers\Interfaces\SuccessHandlerInterface;
use App\Services\TransactionNotifierService;
use Wallet;

class PaymentHandler implements SuccessHandlerInterface
{
    protected TransactionNotifierService $notifier;

    public function __construct(TransactionNotifierService $notifier)
    {
        $this->notifier = $notifier;
    }

    public function handleSuccess(Transaction $transaction): void
    {
        if ($transaction->amount_flow === AmountFlow::MINUS) {
            Wallet::subtractMoneyByWalletUuid($transaction->wallet_reference, $transaction->payable_amount);

            $merchantName = $transaction->trx_data['merchant_name'] ?? 'Merchant';

            $this->notifier->toUser($transaction, 'payment_user_made', [
                'amount'   => $transaction->amount.' '.$transaction->currency,
                'merchant' => $merchantName,
                'trx'      => $transaction->trx_id,
            ]);
        }

        if ($transaction->amount_flow === AmountFlow::PLUS) {
            Wallet::addMoneyByWalletUuid($transaction->wallet_reference, $transaction->net_amount);

            $payerTrx  = Transaction::find($transaction->trx_reference);
            $payerName = $payerTrx?->user?->name ?? 'Payer';

            $transaction->user->notify(new \App\Notifications\TemplateNotification(
                identifier: 'payment_user_received',
                data: [
                    'amount' => $transaction->amount.' '.$transaction->currency,
                    'payer'  => $payerName,
                    'trx'    => $transaction->trx_id,
                ]
            ));
        }
    }
}

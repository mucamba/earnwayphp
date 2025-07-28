<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\TrxType;
use App\Facades\TransactionFacade as Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StatusController extends Controller
{
    /**
     * Handle a successful payment.
     */
    public function success(Request $request): RedirectResponse
    {
        $trxId = $request->input('trx_id');

        if ($trxId) {
            $transaction = Transaction::findTransaction($trxId);

            if ($transaction && $transaction->trx_type === TrxType::RECEIVE_PAYMENT) {
                $redirectUrl = $transaction->trx_data['success_redirect'] ?? null;

                if ($redirectUrl) {
                    return redirect()->away($redirectUrl);
                }
            }
        }

        notifyEvs('success', __('Payment Successful'));

        return redirect()->route('user.transaction.index');
    }

    /**
     * Handle a payment cancellation.
     */
    public function cancel(Request $request): RedirectResponse
    {
        // Retrieve and remove the cancel transaction ID from session if not provided in the request.
        $trxId = $request->input('trx_id') ?: Session::pull('cancel_tnx');

        if (! $trxId) {
            notifyEvs('warning', __('Payment Canceled'));

            return redirect()->route('user.transaction.index');
        }

        $transaction = Transaction::findTransaction($trxId);

        // Only mark the transaction as failed if it exists.
        if ($transaction) {
            Transaction::failTransaction($trxId);
        }

        if ($transaction && $transaction->trx_type === TrxType::RECEIVE_PAYMENT) {
            $redirectUrl = $transaction->trx_data['cancel_redirect'] ?? null;

            if ($redirectUrl) {
                return redirect()->away($redirectUrl);
            }
        }

        notifyEvs('warning', __('Payment Canceled'));

        return redirect()->route('user.transaction.index');
    }
}

<?php

use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\StripeController;
use Illuminate\Support\Facades\Route;

Route::middleware('merchant.auth')->group(function () {
    // Create a payment (stateless; no DB record is stored)
    Route::post('v1/initiate-payment', [PaymentController::class, 'initiatePayment']);
});

Route::middleware('auth:sanctum')
    ->post('/stripe/issuing/ephemeral-key', [StripeController::class, 'createEphemeralKey'])->name('stripe.issuing.ephemeral-key');

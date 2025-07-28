@extends('general.merchant.index')
@section('favicon', asset(setting('site_favicon')))
@section('title', __('Secure Wallet Payment - ') . setting('site_title'))
@section('merchant_content')
    <div class="container d-flex justify-content-center py-5">
        <div class="card shadow-sm border-0 rounded-4 p-4">
            {{-- Total Pay Section (Top Position) --}}
            <div class="bg-light p-3 rounded d-flex align-items-center justify-content-between border-dashed mb-4">
                <img style="height: 30px" src="{{ asset(setting('logo')) }}" alt="{{ setting('site_title') }}" class="img-fluid icon text-primary me-3 fs-3">
                <div class="text-end">
                    <small class="text-muted d-block">{{ __('Total Payable Amount') }}</small>
                    <h4 class="fw-bold text-primary m-0">{{ $data['payment_amount'] }}</h4>
                </div>
            </div>

            {{-- Payment Options Navigation --}}
            <ul class="nav nav-pills nav-justified mb-4" id="paymentTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="wallet-tab" data-bs-toggle="pill" data-bs-target="#wallet-pay" type="button" role="tab" aria-controls="wallet-pay" aria-selected="true">
                        <i class="fas fa-wallet me-1"></i> {{ __('Wallet Payment') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="voucher-tab" data-bs-toggle="pill" data-bs-target="#voucher-pay" type="button" role="tab" aria-controls="voucher-pay" aria-selected="false">
                        <i class="fas fa-ticket-alt me-1"></i> {{ __('Voucher Payment') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="paymentTabContent">
                {{-- Wallet Payment Tab --}}
                <div class="tab-pane fade show active" id="wallet-pay" role="tabpanel" aria-labelledby="wallet-tab">
                    <div class="text-center mb-3">
                        <small class="text-muted d-block">
                            {{ __('Pay using your :site_title :wallet_name Wallet ID or log in for a faster checkout.', ['site_title' => setting('site_title'), 'wallet_name' => $data['currency']]) }}
                        </small>
                        @auth
                            <form action="{{ route('payment.with.account') }}" method="post" class="d-inline">
                                @csrf
                                <input type="hidden" name="trx_id" value="{{ $trxId }}">
                                <button type="submit" class="text-primary border-0 bg-transparent fw-bold text-decoration-none">
                                    <i class="fa-light fa-fingerprint "></i>
                                    {{ __('Login to Pay') }}
                                </button>
                            </form>
                        @else
                            @php $token = Payment::generateToken($trxId); @endphp
                            <a href="{{ route('payment.with.account', ['token' => $token]) }}" class="text-primary fw-bold text-decoration-none">
                                <i class="fa-light fa-fingerprint"></i>
                                {{ __('Login to Pay') }}
                            </a>
                        @endauth
                    </div>

                    <form id="paymentForm" action="{{ route('payment.complete') }}" method="post" novalidate>
                        @csrf
                        <input type="hidden" name="trx_id" value="{{ $trxId }}">
                        <div class="mb-3">
                            <label for="walletID" class="form-label fw-semibold">{{ __('Enter Your :wallet_name Wallet ID',['wallet_name' => $data['currency']]) }}</label>
                            <div class="input-group shadow-sm rounded">
                                <span class="input-group-text bg-light"><i class="fas fa-wallet text-secondary"></i></span>
                                <input id="walletID" name="wallet_id" oninput="this.value = validateNumber(this.value)" type="text" class="form-control border-0" placeholder="{{ __('W/ID: 123456789') }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">{{ __('Enter Login Password') }}</label>
                            <div class="input-group shadow-sm rounded">
                                <span class="input-group-text bg-light"><i class="fas fa-key text-secondary"></i></span>
                                <input id="password" name="password" type="password" class="form-control border-0" placeholder="{{ __('Enter your secure password') }}" required>
                                <button type="button" class="btn btn-light border-0" id="togglePassword" aria-label="{{ __('Show/Hide Password') }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-grid mt-4">
                            <button id="payButton" type="submit" class="btn btn-dark fw-bold rounded-pill" disabled>
                                <span class="spinner-border spinner-border-sm d-none" id="paySpinner"></span>
                                <i class="fas fa-arrow-right me-2"></i> {{ __('Proceed to Payment') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Voucher Payment Tab --}}
                <div class="tab-pane fade" id="voucher-pay" role="tabpanel" aria-labelledby="voucher-tab">
                    <div class="mb-3 text-center">
                        <small class="text-muted">{{ __('Pay instantly using a voucher code.') }}</small>
                    </div>
                    <form id="voucherForm" action="{{ route('payment.complete') }}" method="post" novalidate>
                        @csrf
                        <input type="hidden" name="trx_id" value="{{ $trxId }}">
                        <div class="mb-3">
                            <label for="voucherCode" class="form-label fw-semibold">{{ __('Voucher Code') }}</label>
                            <div class="input-group shadow-sm rounded">
                                <span class="input-group-text bg-light"><i class="fas fa-ticket-alt text-secondary"></i></span>
                                <input id="voucherCode" name="voucher_code" type="text" class="form-control border-0" placeholder="{{ __('Enter your voucher code') }}" required maxlength="32">
                            </div>
                        </div>
                        <div class="d-grid mt-4">
                            <button id="voucherPayButton" type="submit" class="btn btn-success fw-bold rounded-pill">
                                <i class="fas fa-arrow-right me-2"></i> {{ __('Redeem & Pay') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4 small text-muted">
                <i class="fas fa-lock me-1"></i>
                {{ __('Secure Wallet Payment with 256-bit SSL encryption') }}
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            "use strict";
            // Wallet password toggle
            document.getElementById('togglePassword').addEventListener('click', function () {
                const pwd = document.getElementById('password');
                const type = pwd.type === 'password' ? 'text' : 'password';
                pwd.type = type;
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
            // Enable pay button if both fields filled (wallet)
            document.getElementById('paymentForm').addEventListener('input', function () {
                const wallet = document.getElementById('walletID').value.trim();
                const pwd = document.getElementById('password').value.trim();
                document.getElementById('payButton').disabled = !(wallet && pwd);
            });
            // Optimize: Show loader, disable button on submit
            document.getElementById('paymentForm').addEventListener('submit', function (e) {
                const btn = document.getElementById('payButton');
                btn.disabled = true;
                document.getElementById('paySpinner').classList.remove('d-none');
            });
        </script>
    @endpush
@endsection
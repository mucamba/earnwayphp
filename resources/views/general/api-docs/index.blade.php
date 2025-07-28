@extends('frontend.layouts.app')
@push('styles')
	@include('general.api-docs.partials._style')
@endpush
@section('content')
	@php
		$imgUrl = asset(setting('default_breadcrumb_image'));
	@endphp
	<div class="breadcrumb-wrapper bg-cover" style="background-image: url({{$imgUrl}});">
		<div class="container">
			<div class="page-heading">
				<h1 class="wow fadeInUp" data-wow-delay=".3s">{{ __('Merchant API Docs') }}</h1>
				<ul class="breadcrumb-items wow fadeInUp" data-wow-delay=".5s">
					<li>
						<a href="{{  route('home') }}">
							{{ __('Home') }}
						</a>
					</li>
					<li>
						<i class="fas fa-chevron-right"></i>
					</li>
					<li>
						{{ __('Merchant API Docs') }}
					</li>
				</ul>
			</div>
		</div>
	</div>
	
	<section class="about-section section-padding pt-0" id="about">
		@php
			$imgUrl = asset(setting('default_breadcrumb_image'));
			$logoUrl = asset(setting('logo'));
			$navItems = [
				'Overview'            => '#overview',
				'API Credentials'     => '#credentials',
				'Initiate Payment'    => '#initiate',
				'Checkout Redirect'   => '#checkout',
				'IPN Webhook'         => '#ipn',
				'Examples'            => '#examples',
			];
		@endphp

		
		
		<div class="container">
			<div class="row g-0 p-4">
				{{-- Sidebar --}}
				<nav class="col-md-3 sidebar d-none d-md-block">
					@foreach($navItems as $label => $target)
						<a href="{{ $target }}" id="nav-{{ Str::slug($label) }}"  class="{{ $target == '#overview' ? 'active' : '' }}">{{ $label }}</a>
					@endforeach
				</nav>
				
				{{-- Main content --}}
				<main class="col-md-9">
					<div class="content-area">
						<!-- Overview -->
						<section id="overview">
							<h2>Overview</h2>
							<p>Welcome to the EarnWay Merchant API documentation. This guide provides all necessary details for developers to integrate EarnWay wallet and gateway payments into their application, including credential handling, payment initiation, secure IPN handling, and real-time response management.</p>
						</section>
						
						<!-- API Credentials -->
						<section id="credentials">
							<h2>API Credentials</h2>
							<p>Obtain your <strong>Merchant ID</strong>, <strong>API Key</strong>, and <strong>Client Secret</strong> from <code>User Dashboard → Merchant → CONFIG</code>. These credentials are mandatory for authenticating all requests and must be securely stored.</p>
						</section>
						
						<!-- Initiate Payment -->
						<section id="initiate">
							<h2>Initiate Payment</h2>
							<p>Send a <code>POST</code> request to the endpoint below to create a new payment:</p>
							<div class="code-block">
								<pre><code class="language-bash">POST /api/v1/initiate-payment</code></pre>
							</div>
							<h3>Headers</h3>
							<ul>
								<li><code>X-Merchant-Key:</code> Your Merchant ID</li>
								<li><code>X-API-Key:</code> Your API Key</li>
								<li><code>Content-Type:</code> application/json</li>
							</ul>
							<h3>Request Body</h3>
							<div class="code-block">
				<pre><code class="language-json">{
  "payment_amount": 200.00,
  "currency_code": "USD",
  "ref_trx": "TRXqgddgddg",
  "description": "Order #1234",
  "success_redirect": "https://merchant.com/success",
  "failure_url": "https://merchant.com/failure",
  "cancel_redirect": "https://merchant.com/cancel",
  "ipn_url": "https://webhook.site/150456d9-a7fe-49d7-96b8-d08a8adf7ca6"
}</code></pre>
							</div>
							<p><strong>Note:</strong> Use a real URL for <code>ipn_url</code> to receive webhook callbacks.</p>
						</section>
						
						<!-- Checkout Redirect -->
						<section id="checkout">
							<h2>Checkout Redirect</h2>
							<p>After a successful payment initiation, the API returns a <code>payment_url</code>. Redirect your customer to this URL to choose their preferred payment method (wallet, PayPal, Stripe, Mollie, etc.).</p>
							<div class="code-block">
				<pre><code class="language-json">{
  "payment_url": "https://earnway.net/payment/checkout?token=abc123",
  "info": {
    "ref_trx": "TRXqgddgddg",
    "description": "Order #1234",
    "ipn_url": "https://webhook.site/...",
    "cancel_redirect": "https://merchant.com/cancel",
    "success_redirect": "https://merchant.com/success",
    "merchant_id": 6,
    "merchant_name": "Ursula House",
    "amount": 200,
    "currency_code": "USD"
  }
}</code></pre>
							</div>
						</section>
						
						<!-- IPN Webhook -->
						<section id="ipn">
							<h2>IPN Webhook</h2>
							<p>EarnWay sends transaction updates to your defined <code>ipn_url</code>. Each request includes a HMAC signature generated using your <strong>client_secret</strong> to validate authenticity.</p>
							
							<h3>Headers Sent</h3>
							<ul>
								<li><code>Content-Type: application/json</code></li>
								<li><code>X-Signature:</code> HMAC-SHA256 of the entire payload using <code>client_secret</code></li>
							</ul>
							
							<h3>Sample Success IPN</h3>
							<div class="code-block">
				<pre><code class="language-json">{
  "data": {
    "ref_trx": "TRXqgddgddg",
    "description": "Order #1234",
    "ipn_url": "https://webhook.site/150456d9-a7fe-49d7-96b8-d08a8adf7ca6",
    "cancel_redirect": "https://merchant.com/cancel",
    "success_redirect": "https://merchant.com/success",
    "merchant_name": "Ursula House",
    "amount": 200,
    "currency_code": "USD"
  },
  "message": "Payment Completed",
  "status": "completed",
  "timestamp": 1747821208
}</code></pre>
							</div>
							
							<h3>Sample Failed IPN</h3>
							<div class="code-block">
				<pre><code class="language-json">{
  "data": {
    "ref_trx": "TRXqgddgddg",
    "description": "Order #1234",
    "ipn_url": "https://webhook.site/150456d9-a7fe-49d7-96b8-d08a8adf7ca6",
    "cancel_redirect": "https://merchant.com/cancel",
    "success_redirect": "https://merchant.com/success",
    "merchant_name": "Ursula House",
    "amount": 200,
    "currency_code": "USD"
  },
  "message": "Payment Failed",
  "status": "failed",
  "timestamp": 1747820975
}</code></pre>
							</div>
							
							<h3>Laravel IPN Verification Example</h3>
							<div class="code-block">
				<pre><code class="language-php">use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::post('/ipn-listener', function (Request $request) {
    $clientSecret = 'your_client_secret';
    $payload = $request->all();
    $signature = $request->header('X-Signature');
    $expected = hash_hmac('sha256', json_encode($payload), $clientSecret);

    if (! hash_equals($expected, $signature)) {
        Log::warning('Invalid IPN signature.', [
            'expected' => $expected,
            'received' => $signature,
        ]);
        abort(403);
    }

    // Process valid transaction
    return response()->json(['message' => 'Webhook received.']);
});</code></pre>
							</div>
							<p><strong>Best Practice:</strong> Validate the signature and log payloads for auditing. Use background jobs to finalize order updates after IPN.</p>
						</section>
						
						<!-- Examples -->
						<section id="examples">
							<h2>Examples</h2>
							<p>Quick examples for API integration:</p>
							
							<h3>PHP (Laravel HTTP Client)</h3>
							<div class="code-block">
				<pre><code class="language-php">use Illuminate\Support\Facades\Http;

$response = Http::withHeaders([
    'X-Merchant-Key' => env('MERCHANT_ID'),
    'X-API-Key'      => env('API_KEY'),
])->post('https://yourdomain.com/api/v1/initiate-payment', [
    'payment_amount' => 200,
    'currency_code'  => 'USD',
    'ref_trx'        => 'TRXqgddgddg',
]);

return $response->json();</code></pre>
							</div>
							
							
							<h3>cURL CLI</h3>
							<div class="code-block">
				<pre><code class="language-bash">curl -X POST https://yourdomain.com/api/v1/initiate-payment \
  -H "Content-Type: application/json" \
  -H "X-Merchant-Key: $MERCHANT_ID" \
  -H "X-API-Key: $API_KEY" \
  -d '{"payment_amount":200,"currency_code":"USD","ref_trx":"TRXqgddgddg"}'</code></pre>
							</div>
						</section>
					</div>
				</main>
			
			</div>
		</div>
		

	</section>
@endSection
@push('scripts')
	@include('general.api-docs.partials._script')
@endpush

@extends('frontend.layouts.user.index')

@section('title', __('Generate QR Payment Link'))

@section('content')
	<div class="row">
		{{-- Form Section --}}
		<div class="col-lg-12 col-xl-7">
			<div class="single-form-card">
				{{-- Header --}}
				<div class="card-title mb-0 d-flex flex-column flex-md-row justify-content-between">
					<h6 class="text-white mb-2 mb-md-0">{{ __('Generate QR Payment Link') }}</h6>
					<div class="d-flex gap-2 flex-row">
						<a class="btn btn-light-primary btn-sm" href="{{ route('user.merchant.qr-history') }}"><i class="fas fa-list"></i>{{ __('QR Payment History') }}</a>
					</div>
				</div>
				
				{{-- Form Body --}}
				<div class="card-main">
					<form action="{{ route('user.merchant.qr-generate', $merchant->id) }}" method="POST">
						@csrf
						
						<input type="hidden" name="currency" value="{{ $merchant->currency->code }}">
						
						{{-- Amount Field --}}
						<div class="single-input-inner style-border mb-3">
							<label class="form-label">{{ __('Amount to Receive') }}</label>
							<div class="input-group input-group-right">
								<input
									type="text"
									class="form-control"
									name="amount"
									oninput="this.value = validateDouble(this.value)"
									placeholder="{{ __('Enter the amount (e.g. 100.00)') }}"
								>
								<span class="input-group-text input-group-text-right">{{ $merchant->currency->code }}</span>
							</div>
							<span class="small color-base fw-500 span-consistent"></span>
						</div>
						
						<div class="single-input-inner style-border mb-3">
							<label class="form-label">{{ __('Expiration Time (optional)') }}</label>
							<div class="input-group input-group-right">
								<input
									type="text"
									class="form-control"
									name="expire_time"
									oninput="this.value = validateNumber(this.value)"
									placeholder="{{ __('Enter expiration time in minutes') }}"
								>
								<span class="input-group-text input-group-text-right">{{ __('Minutes') }}</span>
							</div>
							<span class="small color-base fw-500 span-consistent">
								{{ __('Leave blank or enter 0 for no expiration. Default is 30 minutes.') }}
							</span>
						</div>
						
						<div class="single-input-inner style-border">
							<label class="form-label">{{ __('Note (Optional)') }}</label>
							<textarea class="rounded" name="note" id="" cols="10" rows="10"></textarea>
						</div>
						
						
						{{-- Submit Button --}}
						<button type="submit" class="btn btn-primary w-100">
							<i class="fas fa-qrcode me-1"></i> {{ __('Generate QR Code') }}
						</button>
					</form>
				</div>
			</div>
		</div>
		
		{{-- QR Preview Section --}}
		<div class="col-lg-12 col-xl-5">
			<div class="single-form-card  d-flex flex-column">
				{{-- Card Header --}}
				<div class="card-title mb-0 d-flex justify-content-between align-items-center">
					<h6 class="text-white mt-2">{{ __('QR Code Preview') }}</h6>
				</div>
				
				{{-- Card Body --}}
				<div class="card-main flex-grow-1 d-flex flex-column justify-content-center align-items-center">
					@if(!empty($qrCode) && !empty($paymentUrl))
						<div class="text-center w-100">
							{{-- QR Code Only (For Print) --}}
							<div id="qrPrintArea"  class="mb-3">
								{!! $qrCode !!}
							</div>
							
							{{-- Payment Info --}}
							<div class="text-dark fw-semibold mb-2">
								{{ __('Amount:') }}
								<span class="text-success">
                            {{ $paymentAmount ?? '' }}
                        </span>
							</div>
							
							{{-- QR Instruction --}}
							<div class="small mb-3 text-muted">
								{{ __('Scan this code to proceed with payment.') }}
							</div>
							
							{{-- Action Buttons --}}
							<div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
								{{-- Copy Button --}}
								<button class="btn btn-primary btn-sm flex-grow-1 copyNow"
								        data-clipboard-text="{{ $paymentUrl }}"
								        title="{{ __('Copy Payment Link') }}"
								        data-bs-toggle="tooltip"
								        data-bs-placement="top">
									<i class="fas fa-copy me-1"></i> {{ __('Copy Link') }}
								</button>
								
								{{-- Print QR Button --}}
								<button class="btn btn-secondary btn-sm flex-grow-1" onclick="printQrCode('qrPrintArea')">
									<i class="fas fa-print me-1"></i> {{ __('Print QR') }}
								</button>
							</div>
						</div>
					@else
						<div class="border rounded p-3 bg-light text-center w-100">
							<i class="fas fa-qrcode fa-3x text-muted"></i>
							<div class="small mt-2 text-muted">{{ __('Your QR code will appear here after generation.') }}</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
@endsection



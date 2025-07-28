@extends('backend.deposit.index')
@section('title',  __('Deposit History'))
@section('deposit_header')
	<div class="clearfix my-3">
		<div class="fs-3 fw-semibold float-start">
			{{ __('Deposit History') }}
		</div>
	</div>
@endSection

@section('deposit_content')
	<div class="card border-0 mb-4">
		<div class="card-body">
			{{-- Filters --}}
			@include('backend.deposit.partials._filter')
			
			{{-- Transactions Table --}}
			<div class="table-responsive">
				<table class="table  border mb-0">
					<thead class="table-light fw-semibold">
					<tr class="align-middle text-nowrap">
						<th>{{ __('User | TXN ID') }}</th>
						<th>{{ __('Amount | Fee') }}</th>
						<th>{{ __('Description | Provider') }}</th>
						<th>{{ __('Status') }}</th>
						<th>{{ __('Time') }}</th>
					</tr>
					</thead>
					<tbody>
					@forelse($depositHistories as $depositHistory)
						@php
							$avatarData = getUserAvatarDetails($depositHistory->user->first_name, $depositHistory->user->last_name);
							$color = $depositHistory->status->color();
							$amountColor = $depositHistory->amount_flow->color($depositHistory->status);
							$amountSign = $depositHistory->amount_flow->sign($depositHistory->status);
						@endphp
						<tr class="align-middle">
							{{-- User Information --}}
							<td>
								<div class="d-flex align-items-center">
									<div class="avatar avatar-md me-2">
										@isset($depositHistory->user->avatar)
											<img class="avatar-img" src="{{ asset($depositHistory->user->avatar) }}" height="40" alt="User Avatar">
										@else
											<div class="avatar avatar-md {{ $avatarData['class'] }} text-white">
												{{ $avatarData['initials'] }}
											</div>
										@endisset
									</div>
									<div>
										<a href="{{ route('admin.user.manage', $depositHistory->user->username) }}" class="text-decoration-none">
											{{ $depositHistory->user->name }}
										</a>
										<div class="small text-muted text-uppercase">{{ strtoupper($depositHistory->trx_id) }}</div>
									</div>
								</div>
							</td>
							
							{{-- Amount Information --}}
							<td>
								<div class="{{ $amountColor }} fw-bold">
									{{ $amountSign . $depositHistory->amount . ' ' . $depositHistory->currency }}
								</div>
								<div class="small text-muted">
									{{ __('Fee: :fee', ['fee' => getSymbol($depositHistory->currency) . $depositHistory->fee]) }}
								</div>
							</td>
							
							{{-- Description & Provider --}}
							<td>
								<div>{{ $depositHistory->description }}</div>
								<div class="small text-muted">{{ $depositHistory->provider .' - '.$depositHistory->processing_type->label() }}</div>
							</td>
							
							{{-- Status --}}
							<td>
								<span class="badge bg-{{ $color }} text-uppercase">{{ $depositHistory->status->label() }}</span>
							</td>
							
							{{-- Transaction Time --}}
							<td>
								<div>{{ $depositHistory->created_at->format('Y-m-d H:i') }}</div>
								<div class="small text-muted">{{ $depositHistory->created_at->diffForHumans() }}</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="5" class="text-center py-3">
								<div class="text-center py-5">
									<x-icon name="no-data-found" height="200"/>
									<h5 class="text-muted mt-2">{{ __('No Data found') }}</h5>
								</div>
							</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>
			
			{{-- Pagination --}}
			<div class="d-flex justify-content-end mt-3">
				{{ $depositHistories->links() }}
			</div>
		</div>
	</div>
@endsection

@php use App\Enums\Transaction\TransactionStatus; @endphp
@extends('layouts.app')

@push('metadata')
	<meta name="robots" content="noindex">
	<meta name="googlebot" content="noindex">
@endpush

@section('content')
	<nav aria-label="breadcrumb">
		<ul>
			<li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
			<li><a href="{{ route('schedule.view', $participant->schedule->slug) }}">{{ $participant->schedule->title }}</a>
			</li>
		</ul>
	</nav>

	@include(sprintf('pages.participants.partials.%s', strtolower($participant->status->value)), compact('participant'))
@endsection
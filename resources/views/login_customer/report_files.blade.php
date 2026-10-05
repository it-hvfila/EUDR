@extends('layouts.master')
@section('title', 'HVF | Report Files')
@section('content')
<main class="container py-5">
    <a href="{{ route('customer.dashboard') }}">Your Reports</a>
    <h1 class="h3 mt-3">Invoice {{ $report->invoice_no }} / Pack ID {{ $report->pack_id }}</h1>
    <p>Access expires: {{ $report->expired_at }}</p>
    <ul class="list-group">
        @forelse ($files as $file)
            <li class="list-group-item"><a href="{{ route('customer.report.download', [$report->token, $file->token]) }}">{{ $file->file_name }}</a></li>
        @empty
            <li class="list-group-item">No supporting files available.</li>
        @endforelse
    </ul>
</main>
@endsection

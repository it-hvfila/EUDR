@extends('layouts.master')
@section('title', 'HVF | Customer Reports')

@section('content')
<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3">Customer EUDR Portal</h1>
            <p class="mb-0">{{ session('customer.name') }}</p>
        </div>
        <form method="POST" action="{{ route('customer.logout') }}">
            @csrf
            <button class="btn btn-outline-secondary" type="submit">Logout</button>
        </form>
    </div>
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Your Reports</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Pack ID</th><th>Invoice</th><th>Created</th><th>Access expires</th><th>Files</th></tr></thead>
                    <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td>{{ $report->pack_id }}</td>
                            <td>{{ $report->invoice_no ?? '-' }}</td>
                            <td>{{ $report->created_at }}</td>
                            <td>{{ $report->expired_at }}</td>
                            <td><a class="btn btn-sm btn-primary" href="{{ route('customer.report.files', $report->token) }}">View report</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No reports are available for your account. Please contact your account representative.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $reports->links() }}
        </div>
    </div>
</main>
@endsection

@extends('layouts.app')

@section('title', 'Queue - Registration')

@section('page-title', 'Queue Management')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Queue</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Register New Patient</h3>
                    <div class="card-tools">
                        <a href="{{ route('registration.queue.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Register Offline
                        </a>
                    </div>
                </div>
            </div>

            @foreach ($specializations as $specialization)
                @php
                    $called = $specialization->queues()
                        ->where('status', 'called')
                        ->whereDate('created_at', now()->startOfDay())
                        ->with('visit.patient')
                        ->orderBy('called_at', 'desc')
                        ->first();
                    $waiting = $specialization->queues()
                        ->whereIn('status', ['waiting', 'waiting_online_confirmation'])
                        ->whereDate('created_at', now()->startOfDay())
                        ->with('visit.patient')
                        ->orderBy('queue_number')
                        ->get();
                @endphp

                <div class="card card-primary card-outline mb-4">
                    <div class="card-header">
                        <div class="card-title">{{ $specialization->name }}</div>
                    </div>
                    <div class="card-body">
                        @if ($called)
                            <div class="alert alert-success">
                                <strong>Now serving:</strong> Antrian No. {{ $called->queue_number }} - {{ $called->visit->patient->name ?? 'N/A' }}
                            </div>
                        @endif

                        @if ($waiting->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>No. Antrian</th>
                                            <th>Patient</th>
                                            <th>Source</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($waiting as $queue)
                                            <tr class="{{ $queue->status === 'waiting_online_confirmation' ? 'table-warning' : '' }}">
                                                <td>{{ $queue->queue_number }}</td>
                                                <td>{{ $queue->visit->patient->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($queue->source === 'online')
                                                        <span class="badge bg-info">Online</span>
                                                    @else
                                                        <span class="badge bg-secondary">Offline</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($queue->status === 'waiting_online_confirmation')
                                                        <span class="badge bg-warning">Belum Check-in</span>
                                                    @else
                                                        <span class="badge bg-success">Menunggu</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($queue->status === 'waiting_online_confirmation')
                                                        <form action="{{ route('registration.queue.check-in', $queue->visit) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="bi bi-check-lg"></i> Check-in
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('registration.queue.skip', $queue->visit) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Skip this patient?')">
                                                                <i class="bi bi-x-lg"></i> Skip
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('registration.queue.call', $queue->visit) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-primary">
                                                                <i class="bi bi-megaphone"></i> Call
                                                            </button>
                                                        </form>
                                                        <a href="{{ route('registration.vitals.create', $queue->visit) }}" class="btn btn-sm btn-info">
                                                            <i class="bi bi-heart-pulse"></i> TTV
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">No patients waiting.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Activity Log')

@section('page-title', 'Activity Log')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Activity Log</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Activity Log</h3>
                    <div class="card-tools">
                        <form action="{{ route('activity-log.index') }}" method="GET" class="input-group" style="width: 300px;">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>User</th>
                                <th>Event</th>
                                <th>Description</th>
                                <th>Subject</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $activity->causer ? $activity->causer->name : 'System' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $activity->event ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $activity->description }}</td>
                                    <td>
                                        @if ($activity->subject_type)
                                            <small class="text-muted">
                                                {{ class_basename($activity->subject_type) }}
                                                #{{ $activity->subject_id }}
                                            </small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No activity logs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $activities->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

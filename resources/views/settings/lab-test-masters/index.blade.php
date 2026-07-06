@extends('layouts.app')

@section('title', 'Lab Test Masters - Settings')

@section('page-title', 'Lab Test Masters')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lab Test Masters</li>
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

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lab Test Masters List</h3>
                    <div class="card-tools">
                        @can('settings.lab-test-master.create')
                            <a href="{{ route('settings.lab-test-masters.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle"></i> Add Lab Test Master
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Normal Range</th>
                                <th>Tariff</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($labTestMasters as $labTestMaster)
                                <tr>
                                    <td><strong>{{ $labTestMaster->name }}</strong></td>
                                    <td>
                                        @if ($labTestMaster->category === 'lab')
                                            <span class="badge bg-info">Lab</span>
                                        @elseif ($labTestMaster->category === 'radiology')
                                            <span class="badge bg-warning text-dark">Radiology</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($labTestMaster->category) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $labTestMaster->unit ?? '-' }}</td>
                                    <td>
                                        @if ($labTestMaster->normal_range_min !== null && $labTestMaster->normal_range_max !== null)
                                            {{ $labTestMaster->normal_range_min }} - {{ $labTestMaster->normal_range_max }}
                                        @elseif ($labTestMaster->normal_range_min !== null)
                                            &ge; {{ $labTestMaster->normal_range_min }}
                                        @elseif ($labTestMaster->normal_range_max !== null)
                                            &le; {{ $labTestMaster->normal_range_max }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $labTestMaster->tariff?->name ?? '-' }}</td>
                                    <td>
                                        @can('settings.lab-test-master.edit')
                                            <a href="{{ route('settings.lab-test-masters.edit', $labTestMaster) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('settings.lab-test-master.delete')
                                            <form action="{{ route('settings.lab-test-masters.destroy', $labTestMaster) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No lab test masters found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Tariffs - Settings')

@section('page-title', 'Tariffs')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tariffs</li>
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
                    <h3 class="card-title">Tariffs List</h3>
                    <div class="card-tools">
                        @can('settings.tariff.create')
                            <a href="{{ route('settings.tariffs.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle"></i> Add Tariff
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Tariff Type</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tariffs as $tariff)
                                <tr>
                                    <td>
                                        @php
                                            $typeLabels = [
                                                'tuslah' => ['label' => 'Tuslah', 'class' => 'bg-primary'],
                                                'embalase' => ['label' => 'Embalase', 'class' => 'bg-info'],
                                                'procedure' => ['label' => 'Procedure', 'class' => 'bg-warning text-dark'],
                                                'doctor_fee' => ['label' => 'Doctor Fee', 'class' => 'bg-success'],
                                                'other' => ['label' => 'Other', 'class' => 'bg-secondary'],
                                            ];
                                            $type = $typeLabels[$tariff->tariff_type] ?? ['label' => ucfirst($tariff->tariff_type), 'class' => 'bg-secondary'];
                                        @endphp
                                        <span class="badge {{ $type['class'] }}">{{ $type['label'] }}</span>
                                    </td>
                                    <td><strong>{{ $tariff->name }}</strong></td>
                                    <td>Rp {{ number_format($tariff->amount, 2, ',', '.') }}</td>
                                    <td>
                                        @if ($tariff->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('settings.tariff.edit')
                                            <a href="{{ route('settings.tariffs.edit', $tariff) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('settings.tariff.delete')
                                            <form action="{{ route('settings.tariffs.destroy', $tariff) }}" method="POST" style="display:inline;">
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
                                    <td colspan="5" class="text-center">No tariffs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

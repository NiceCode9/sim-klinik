@extends('layouts.app')

@section('title', 'Doctor Tariffs - Settings')

@section('page-title', 'Doctor Tariffs')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Doctor Tariffs</li>
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
                    <h3 class="card-title">Doctor Tariffs List</h3>
                    <div class="card-tools">
                        @can('settings.doctor-tariff.create')
                            <a href="{{ route('settings.doctor-tariffs.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle"></i> Add Doctor Tariff
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Doctor Name</th>
                                <th>Specialization</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($doctorTariffs as $doctorTariff)
                                <tr>
                                    <td><strong>{{ $doctorTariff->employee?->name ?? '-' }}</strong></td>
                                    <td>{{ $doctorTariff->specialization?->name ?? '-' }}</td>
                                    <td>Rp {{ number_format($doctorTariff->amount, 2, ',', '.') }}</td>
                                    <td>
                                        @can('settings.doctor-tariff.edit')
                                            <a href="{{ route('settings.doctor-tariffs.edit', $doctorTariff) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('settings.doctor-tariff.delete')
                                            <form action="{{ route('settings.doctor-tariffs.destroy', $doctorTariff) }}" method="POST" style="display:inline;">
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
                                    <td colspan="4" class="text-center">No doctor tariffs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

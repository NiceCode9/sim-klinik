@extends('layouts.app')

@section('title', 'Drugs - Settings')

@section('page-title', 'Drugs')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Drugs</li>
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
                    <h3 class="card-title">Drugs List</h3>
                    <div class="card-tools">
                        @can('settings.drug.create')
                            <a href="{{ route('settings.drugs.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle"></i> Add Drug
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Pricing Type</th>
                                <th>Price Value</th>
                                <th>Min Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($drugs as $drug)
                                <tr>
                                    <td><code>{{ $drug->code }}</code></td>
                                    <td><strong>{{ $drug->name }}</strong></td>
                                    <td>{{ $drug->category ?? '-' }}</td>
                                    <td>{{ $drug->unit }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $drug->pricing_type)) }}</td>
                                    <td>Rp {{ number_format($drug->price_value, 2, ',', '.') }}</td>
                                    <td>{{ $drug->minimum_stock }}</td>
                                    <td>
                                        @if ($drug->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('settings.drug.edit')
                                            <a href="{{ route('settings.drugs.edit', $drug) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('settings.drug.delete')
                                            <form action="{{ route('settings.drugs.destroy', $drug) }}" method="POST" style="display:inline;">
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
                                    <td colspan="9" class="text-center">No drugs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

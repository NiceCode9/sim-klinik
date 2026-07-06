@extends('layouts.app')

@section('title', 'Patients')

@section('page-title', 'Patients')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Patients</li>
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
                    <h3 class="card-title">Patients List</h3>
                    <div class="card-tools">
                        @can('patient.create')
                            <a href="{{ route('patients.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle"></i> Add Patient
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Medical Record Number</th>
                                <th>NIK</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Birth Date</th>
                                <th>Phone</th>
                                <th>Blood Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($patients as $patient)
                                <tr>
                                    <td><code>{{ $patient->medical_record_number }}</code></td>
                                    <td>{{ $patient->nik ?? '-' }}</td>
                                    <td><strong>{{ $patient->name }}</strong></td>
                                    <td>
                                        @if ($patient->gender === 'L')
                                            <span class="badge bg-info">Pria</span>
                                        @else
                                            <span class="badge bg-primary">Wanita</span>
                                        @endif
                                    </td>
                                    <td>{{ $patient->birth_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ $patient->phone ?? '-' }}</td>
                                    <td>{{ $patient->blood_type ?? '-' }}</td>
                                    <td>
                                        @can('patient.edit')
                                            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('patient.delete')
                                            <form action="{{ route('patients.destroy', $patient) }}" method="POST" style="display:inline;">
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
                                    <td colspan="8" class="text-center">No patients found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

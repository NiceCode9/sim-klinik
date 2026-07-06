@extends('layouts.app')

@section('title', 'Employees - Settings')

@section('page-title', 'Employees')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Employees</li>
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
                    <h3 class="card-title">Employees List</h3>
                    <div class="card-tools">
                        @can('settings.employee.create')
                            <a href="{{ route('settings.employees.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle"></i> Add Employee
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Employee Type</th>
                                <th>Specialization</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($employees as $employee)
                                <tr>
                                    <td><strong>{{ $employee->name }}</strong></td>
                                    <td>{{ ucfirst($employee->employee_type) }}</td>
                                    <td>{{ $employee->specialization?->name ?? '-' }}</td>
                                    <td>{{ $employee->phone ?? '-' }}</td>
                                    <td>
                                        @if ($employee->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('settings.employee.edit')
                                            <a href="{{ route('settings.employees.edit', $employee) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('settings.employee.delete')
                                            <form action="{{ route('settings.employees.destroy', $employee) }}" method="POST" style="display:inline;">
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
                                    <td colspan="6" class="text-center">No employees found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

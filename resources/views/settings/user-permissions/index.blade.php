@extends('layouts.app')

@section('title', 'User Permissions - Settings')

@section('page-title', 'User Permissions Management')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item active" aria-current="page">User Permissions</li>
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Users List</h3>
                    <p class="card-text"><small class="text-muted">Assign direct permissions to users (override role permissions)</small></p>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Direct Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td><strong>{{ $user->name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->roles->count() > 0)
                                            @foreach ($user->roles as $role)
                                                <span class="badge bg-primary">{{ $role->name }}</span>
                                            @endforeach
                                        @else
                                            <small class="text-muted">No roles</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->permissions->count() > 0)
                                            <small>{{ $user->permissions->pluck('name')->implode(', ') }}</small>
                                        @else
                                            <small class="text-muted">No direct permissions</small>
                                        @endif
                                    </td>
                                    <td>
                                        @can('settings.permission.assign')
                                            <a href="{{ route('settings.user-permissions.edit', $user) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-pencil"></i> Assign Permissions
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

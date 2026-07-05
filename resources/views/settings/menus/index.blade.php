@extends('layouts.app')

@section('title', 'Menus - Settings')

@section('page-title', 'Menus Management')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Menus</li>
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
                    <h3 class="card-title">Menus List</h3>
                    <div class="card-tools">
                        @can('settings.menu.create')
                            <a href="{{ route('settings.menus.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle"></i> Add Menu
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Icon</th>
                                <th>Route</th>
                                <th>Permission</th>
                                <th>Order</th>
                                <th>Active</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($menus as $menu)
                                <tr>
                                    <td><strong>{{ $menu->name }}</strong></td>
                                    <td><i class="{{ $menu->icon }}"></i></td>
                                    <td>{{ $menu->route_name ?? '-' }}</td>
                                    <td>{{ $menu->permission_name ?? '-' }}</td>
                                    <td>{{ $menu->order }}</td>
                                    <td>
                                        @if ($menu->is_active)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-danger">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('settings.menu.edit')
                                            <a href="{{ route('settings.menus.edit', $menu) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('settings.menu.delete')
                                            <form action="{{ route('settings.menus.destroy', $menu) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                                @if ($menu->children->count() > 0)
                                    @foreach ($menu->children as $submenu)
                                        <tr style="background-color: #f8f9fa;">
                                            <td style="padding-left: 30px;">├─ {{ $submenu->name }}</td>
                                            <td><i class="{{ $submenu->icon }}"></i></td>
                                            <td>{{ $submenu->route_name ?? '-' }}</td>
                                            <td>{{ $submenu->permission_name ?? '-' }}</td>
                                            <td>{{ $submenu->order }}</td>
                                            <td>
                                                @if ($submenu->is_active)
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-danger">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @can('settings.menu.edit')
                                                    <a href="{{ route('settings.menus.edit', $submenu) }}" class="btn btn-sm btn-info">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endcan
                                                @can('settings.menu.delete')
                                                    <form action="{{ route('settings.menus.destroy', $submenu) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No menus found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

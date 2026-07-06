@extends('layouts.app')

@section('title', 'Create Employee - Settings')

@section('page-title', 'Create Employee')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item"><a href="{{ route('settings.employees.index') }}">Employees</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Create New Employee</h3>
                </div>
                <form action="{{ route('settings.employees.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_type" class="form-label">Employee Type <span class="text-danger">*</span></label>
                                    <select name="employee_type" id="employee_type" class="form-select @error('employee_type') is-invalid @enderror" required>
                                        <option value="">-- Select Type --</option>
                                        @foreach (App\Enums\EmployeeType::cases() as $type)
                                            <option value="{{ $type->value }}" {{ old('employee_type') == $type->value ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">User Account</label>
                                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                        <option value="">-- None --</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="specialization_id" class="form-label">Specialization</label>
                                    <select name="specialization_id" id="specialization_id" class="form-select @error('specialization_id') is-invalid @enderror">
                                        <option value="">-- None --</option>
                                        @foreach ($specializations as $spec)
                                            <option value="{{ $spec->id }}" {{ old('specialization_id') == $spec->id ? 'selected' : '' }}>
                                                {{ $spec->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('specialization_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="str_number" class="form-label">STR Number</label>
                                    <input type="text" name="str_number" id="str_number" class="form-control @error('str_number') is-invalid @enderror" value="{{ old('str_number') }}">
                                    @error('str_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input @error('is_active') is-invalid @enderror" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Employee</button>
                        <a href="{{ route('settings.employees.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

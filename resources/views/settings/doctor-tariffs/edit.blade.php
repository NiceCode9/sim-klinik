@extends('layouts.app')

@section('title', 'Edit Doctor Tariff - Settings')

@section('page-title', 'Edit Doctor Tariff')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item"><a href="{{ route('settings.doctor-tariffs.index') }}">Doctor Tariffs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Edit Doctor Tariff</h3>
                </div>
                <form action="{{ route('settings.doctor-tariffs.update', $doctorTariff) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Doctor <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                        <option value="">-- Select Doctor --</option>
                                        @foreach ($doctors as $doctor)
                                            <option value="{{ $doctor->id }}" {{ old('employee_id', $doctorTariff->employee_id) == $doctor->id ? 'selected' : '' }}>
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
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
                                            <option value="{{ $spec->id }}" {{ old('specialization_id', $doctorTariff->specialization_id) == $spec->id ? 'selected' : '' }}>
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

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $doctorTariff->amount) }}" step="0.01" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Doctor Tariff</button>
                        <a href="{{ route('settings.doctor-tariffs.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

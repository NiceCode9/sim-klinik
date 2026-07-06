@extends('layouts.app')

@section('title', 'Create Tariff - Settings')

@section('page-title', 'Create Tariff')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item"><a href="{{ route('settings.tariffs.index') }}">Tariffs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Create New Tariff</h3>
                </div>
                <form action="{{ route('settings.tariffs.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tariff_type" class="form-label">Tariff Type <span class="text-danger">*</span></label>
                                    <select name="tariff_type" id="tariff_type" class="form-select @error('tariff_type') is-invalid @enderror" required>
                                        <option value="">-- Select Type --</option>
                                        <option value="tuslah" {{ old('tariff_type') == 'tuslah' ? 'selected' : '' }}>Tuslah</option>
                                        <option value="embalase" {{ old('tariff_type') == 'embalase' ? 'selected' : '' }}>Embalase</option>
                                        <option value="procedure" {{ old('tariff_type') == 'procedure' ? 'selected' : '' }}>Procedure</option>
                                        <option value="doctor_fee" {{ old('tariff_type') == 'doctor_fee' ? 'selected' : '' }}>Doctor Fee</option>
                                        <option value="other" {{ old('tariff_type') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('tariff_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" step="0.01" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input @error('is_active') is-invalid @enderror" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Tariff</button>
                        <a href="{{ route('settings.tariffs.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

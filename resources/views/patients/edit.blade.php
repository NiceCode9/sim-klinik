@extends('layouts.app')

@section('title', 'Edit Patient')

@section('page-title', 'Edit Patient')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Edit Patient: {{ $patient->name }}</h3>
                </div>
                <form action="{{ route('patients.update', $patient) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nik" class="form-label">NIK</label>
                                    <input type="text" name="nik" id="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $patient->nik) }}">
                                    @error('nik')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $patient->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="L" {{ old('gender', $patient->gender) == 'L' ? 'selected' : '' }}>Pria</option>
                                        <option value="P" {{ old('gender', $patient->gender) == 'P' ? 'selected' : '' }}>Wanita</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Birth Date <span class="text-danger">*</span></label>
                                    <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', $patient->birth_date?->format('Y-m-d')) }}" required>
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $patient->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="blood_type" class="form-label">Blood Type</label>
                                    <select name="blood_type" id="blood_type" class="form-select @error('blood_type') is-invalid @enderror">
                                        <option value="">-- Select Blood Type --</option>
                                        <option value="A" {{ old('blood_type', $patient->blood_type) == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('blood_type', $patient->blood_type) == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="AB" {{ old('blood_type', $patient->blood_type) == 'AB' ? 'selected' : '' }}>AB</option>
                                        <option value="O" {{ old('blood_type', $patient->blood_type) == 'O' ? 'selected' : '' }}>O</option>
                                    </select>
                                    @error('blood_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $patient->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="allergies" class="form-label">Allergies</label>
                            <textarea name="allergies" id="allergies" class="form-control @error('allergies') is-invalid @enderror" rows="3">{{ old('allergies', $patient->allergies) }}</textarea>
                            @error('allergies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Patient</button>
                        <a href="{{ route('patients.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

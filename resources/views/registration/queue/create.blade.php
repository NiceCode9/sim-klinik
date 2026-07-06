@extends('layouts.app')

@section('title', 'Register Patient - Queue')

@section('page-title', 'Register New Patient')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('registration.queue.index') }}">Queue</a></li>
            <li class="breadcrumb-item active" aria-current="page">Register</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                    <div class="card-title">Register Offline Patient</div>
                </div>
                <form action="{{ route('registration.queue.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="patient_id" class="form-label">Patient <span class="text-danger">*</span></label>
                            <select name="patient_id" id="patient_id" class="form-select" required>
                                <option value="">-- Select Patient --</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->medical_record_number }} - {{ $patient->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="specialization_id" class="form-label">Poly / Specialization <span class="text-danger">*</span></label>
                            <select name="specialization_id" id="specialization_id" class="form-select" required>
                                <option value="">-- Select Poly --</option>
                                @foreach ($specializations as $spec)
                                    <option value="{{ $spec->id }}" {{ old('specialization_id') == $spec->id ? 'selected' : '' }}>
                                        {{ $spec->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Register
                        </button>
                        <a href="{{ route('registration.queue.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <div class="card-title">Quick Search / New Patient</div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Patient not in the list? <a href="{{ route('patients.create') }}">Create new patient</a> first.</p>
                    <p class="text-muted mb-0">Search by NIK or name from the patient master data.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

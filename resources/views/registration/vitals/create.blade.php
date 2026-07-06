@extends('layouts.app')

@section('title', 'Input TTV - Registration')

@section('page-title', 'Input Vital Signs')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('registration.queue.index') }}">Queue</a></li>
            <li class="breadcrumb-item active" aria-current="page">TTV</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                    <div class="card-title">
                        Vital Signs - {{ $visit->patient->name ?? 'N/A' }}
                        <small class="text-muted">(No. Antrian: {{ $visit->queue->queue_number ?? '-' }})</small>
                    </div>
                </div>
                <form action="{{ route('registration.vitals.store', $visit) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="blood_pressure" class="form-label">Blood Pressure</label>
                                    <input type="text" name="blood_pressure" id="blood_pressure" class="form-control" placeholder="120/80" value="{{ old('blood_pressure') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="pulse" class="form-label">Pulse (bpm)</label>
                                    <input type="text" name="pulse" id="pulse" class="form-control" placeholder="80" value="{{ old('pulse') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="temperature" class="form-label">Temperature (°C)</label>
                                    <input type="text" name="temperature" id="temperature" class="form-control" placeholder="36.5" value="{{ old('temperature') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="respiration_rate" class="form-label">Respiration Rate</label>
                                    <input type="text" name="respiration_rate" id="respiration_rate" class="form-control" placeholder="20" value="{{ old('respiration_rate') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="height_cm" class="form-label">Height (cm)</label>
                                    <input type="number" name="height_cm" id="height_cm" class="form-control" step="0.1" placeholder="165" value="{{ old('height_cm') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="weight_kg" class="form-label">Weight (kg)</label>
                                    <input type="number" name="weight_kg" id="weight_kg" class="form-control" step="0.1" placeholder="65" value="{{ old('weight_kg') }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="chief_complaint" class="form-label">Chief Complaint</label>
                            <textarea name="chief_complaint" id="chief_complaint" class="form-control" rows="3" placeholder="Patient's main complaint...">{{ old('chief_complaint') }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save & Send to Doctor
                        </button>
                        <a href="{{ route('registration.queue.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <div class="card-title">Patient Info</div>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Name</dt>
                        <dd>{{ $visit->patient->name ?? 'N/A' }}</dd>
                        <dt>No. RM</dt>
                        <dd>{{ $visit->patient->medical_record_number ?? 'N/A' }}</dd>
                        <dt>Poly</dt>
                        <dd>{{ $visit->specialization->name ?? 'N/A' }}</dd>
                        <dt>No. Antrian</dt>
                        <dd>{{ $visit->queue->queue_number ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection

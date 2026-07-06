@extends('layouts.app')

@section('title', 'Create Procedure Tariff - Settings')

@section('page-title', 'Create Procedure Tariff')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item"><a href="{{ route('settings.procedure-tariffs.index') }}">Procedure Tariffs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Create New Procedure Tariff</h3>
                </div>
                <form action="{{ route('settings.procedure-tariffs.store') }}" method="POST">
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
                                    <label for="icd9cm_code_id" class="form-label">ICD-9-CM Code</label>
                                    <select name="icd9cm_code_id" id="icd9cm_code_id" class="form-select @error('icd9cm_code_id') is-invalid @enderror">
                                        <option value="">-- None --</option>
                                        @foreach ($icd9cmCodes as $code)
                                            <option value="{{ $code->id }}" {{ old('icd9cm_code_id') == $code->id ? 'selected' : '' }}>
                                                {{ $code->code }} - {{ $code->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('icd9cm_code_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" step="0.01" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Procedure Tariff</button>
                        <a href="{{ route('settings.procedure-tariffs.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

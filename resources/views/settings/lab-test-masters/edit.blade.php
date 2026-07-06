@extends('layouts.app')

@section('title', 'Edit Lab Test Master - Settings')

@section('page-title', 'Edit Lab Test Master')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item"><a href="{{ route('settings.lab-test-masters.index') }}">Lab Test Masters</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Edit Lab Test Master: {{ $labTestMaster->name }}</h3>
                </div>
                <form action="{{ route('settings.lab-test-masters.update', $labTestMaster) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $labTestMaster->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="lab" {{ old('category', $labTestMaster->category) == 'lab' ? 'selected' : '' }}>Lab</option>
                                        <option value="radiology" {{ old('category', $labTestMaster->category) == 'radiology' ? 'selected' : '' }}>Radiology</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit</label>
                                    <input type="text" name="unit" id="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $labTestMaster->unit) }}">
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="normal_range_min" class="form-label">Normal Range Min</label>
                                    <input type="number" name="normal_range_min" id="normal_range_min" class="form-control @error('normal_range_min') is-invalid @enderror" value="{{ old('normal_range_min', $labTestMaster->normal_range_min) }}" step="0.01">
                                    @error('normal_range_min')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="normal_range_max" class="form-label">Normal Range Max</label>
                                    <input type="number" name="normal_range_max" id="normal_range_max" class="form-control @error('normal_range_max') is-invalid @enderror" value="{{ old('normal_range_max', $labTestMaster->normal_range_max) }}" step="0.01">
                                    @error('normal_range_max')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tariff_id" class="form-label">Tariff</label>
                            <select name="tariff_id" id="tariff_id" class="form-select @error('tariff_id') is-invalid @enderror">
                                <option value="">-- None --</option>
                                @foreach ($tariffs as $tariff)
                                    <option value="{{ $tariff->id }}" {{ old('tariff_id', $labTestMaster->tariff_id) == $tariff->id ? 'selected' : '' }}>
                                        {{ $tariff->name }} (Rp {{ number_format($tariff->amount, 2, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('tariff_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Lab Test Master</button>
                        <a href="{{ route('settings.lab-test-masters.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

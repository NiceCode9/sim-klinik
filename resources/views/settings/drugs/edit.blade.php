@extends('layouts.app')

@section('title', 'Edit Drug - Settings')

@section('page-title', 'Edit Drug')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item"><a href="{{ route('settings.drugs.index') }}">Drugs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Edit Drug: {{ $drug->name }}</h3>
                </div>
                <form action="{{ route('settings.drugs.update', $drug) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $drug->code) }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $drug->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="Obat Bebas" {{ old('category', $drug->category) == 'Obat Bebas' ? 'selected' : '' }}>Obat Bebas</option>
                                        <option value="Obat Keras" {{ old('category', $drug->category) == 'Obat Keras' ? 'selected' : '' }}>Obat Keras</option>
                                        <option value="Narkotika" {{ old('category', $drug->category) == 'Narkotika' ? 'selected' : '' }}>Narkotika</option>
                                        <option value="Generik" {{ old('category', $drug->category) == 'Generik' ? 'selected' : '' }}>Generik</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit <span class="text-danger">*</span></label>
                                    <select name="unit" id="unit" class="form-select @error('unit') is-invalid @enderror" required>
                                        <option value="">-- Select Unit --</option>
                                        <option value="tablet" {{ old('unit', $drug->unit) == 'tablet' ? 'selected' : '' }}>Tablet</option>
                                        <option value="botol" {{ old('unit', $drug->unit) == 'botol' ? 'selected' : '' }}>Botol</option>
                                        <option value="ml" {{ old('unit', $drug->unit) == 'ml' ? 'selected' : '' }}>ml</option>
                                        <option value="mg" {{ old('unit', $drug->unit) == 'mg' ? 'selected' : '' }}>mg</option>
                                        <option value="kapsul" {{ old('unit', $drug->unit) == 'kapsul' ? 'selected' : '' }}>Kapsul</option>
                                        <option value="sirup" {{ old('unit', $drug->unit) == 'sirup' ? 'selected' : '' }}>Sirup</option>
                                        <option value="salep" {{ old('unit', $drug->unit) == 'salep' ? 'selected' : '' }}>Salep</option>
                                        <option value="injeksi" {{ old('unit', $drug->unit) == 'injeksi' ? 'selected' : '' }}>Injeksi</option>
                                        <option value="lain" {{ old('unit', $drug->unit) == 'lain' ? 'selected' : '' }}>Lain</option>
                                    </select>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pricing_type" class="form-label">Pricing Type <span class="text-danger">*</span></label>
                                    <select name="pricing_type" id="pricing_type" class="form-select @error('pricing_type') is-invalid @enderror" required>
                                        <option value="">-- Select Pricing Type --</option>
                                        @foreach (App\Enums\PricingType::cases() as $type)
                                            <option value="{{ $type->value }}" {{ old('pricing_type', $drug->pricing_type) == $type->value ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $type->name)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('pricing_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price_value" class="form-label">Price Value <span class="text-danger">*</span></label>
                                    <input type="number" name="price_value" id="price_value" class="form-control @error('price_value') is-invalid @enderror" value="{{ old('price_value', $drug->price_value) }}" step="0.01" required>
                                    @error('price_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="minimum_stock" class="form-label">Minimum Stock</label>
                                    <input type="number" name="minimum_stock" id="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" value="{{ old('minimum_stock', $drug->minimum_stock) }}" step="0.01">
                                    @error('minimum_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" name="is_fractional" id="is_fractional" class="form-check-input @error('is_fractional') is-invalid @enderror" value="1" {{ old('is_fractional', $drug->is_fractional) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_fractional">Fractional</label>
                                        @error('is_fractional')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input @error('is_active') is-invalid @enderror" value="1" {{ old('is_active', $drug->is_active) ? 'checked' : '' }}>
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
                        <button type="submit" class="btn btn-primary">Update Drug</button>
                        <a href="{{ route('settings.drugs.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

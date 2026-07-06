@extends('layouts.public')

@section('title', 'Online Registration')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header text-center">
                    <h3 class="card-title">Online Registration</h3>
                </div>
                <form action="{{ route('register.online.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <p class="text-muted">Register online to get a queue number. Please check-in upon arrival.</p>

                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK (optional)</label>
                            <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik') }}" maxlength="20">
                            <small class="text-muted">Provide NIK if you have registered before.</small>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" id="gender" class="form-select" required>
                                        <option value="">-- Select --</option>
                                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Male</option>
                                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Birth Date <span class="text-danger">*</span></label>
                                    <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}" required>
                                    @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="2">{{ old('address') }}</textarea>
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
                            @error('specialization_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg"></i> Register
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

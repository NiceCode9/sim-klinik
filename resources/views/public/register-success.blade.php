@extends('layouts.public')

@section('title', 'Registration Successful')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header text-center">
                    <h3 class="card-title">Registration Successful</h3>
                </div>
                <div class="card-body text-center">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <p class="mt-3">
                        <a href="{{ route('register.online.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Register Another
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

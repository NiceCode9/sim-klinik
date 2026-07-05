@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Welcome to SIMKLINIK</h3>
                </div>
                <div class="card-body">
                    <p>Sistem Informasi Manajemen Klinik - Dashboard</p>
                    <p>You're logged in as <strong>{{ auth()->user()->name }}</strong></p>
                </div>
            </div>
        </div>
    </div>
@endsection

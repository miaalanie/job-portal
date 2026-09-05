@extends('layouts.admin')

@section('title', 'Health ML Service')
@section('page_title', 'Health ML Service')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title">Status Koneksi Mesin Rekomendasi</h3>
        <div class="card-toolbar">
            <span class="badge {{ $health['reachable'] ? 'badge-light-success' : 'badge-light-danger' }} fs-6">
                {{ $health['reachable'] ? 'Terhubung' : 'Bermasalah' }}
            </span>
        </div>
    </div>
    <div class="card-body">
        @if($health['reachable'])
            <div class="row g-5">
                <div class="col-md-4"><div class="border rounded p-5 h-100"><div class="text-muted">Status HTTP</div><div class="fs-2 fw-bold">{{ $health['status'] }}</div></div></div>
                <div class="col-md-4"><div class="border rounded p-5 h-100"><div class="text-muted">Model embedding</div><div class="fs-5 fw-bold">{{ $health['data']['model'] ?? '-' }}</div></div></div>
                <div class="col-md-4"><div class="border rounded p-5 h-100"><div class="text-muted">Dimensi embedding</div><div class="fs-2 fw-bold">{{ $health['data']['embedding_dimension'] ?? '-' }}</div></div></div>
            </div>
            <div class="alert alert-success mt-6 mb-0">ML service merespons normal dan siap menerima proses rekomendasi.</div>
        @else
            <div class="alert alert-danger">
                <h4 class="alert-heading">ML service tidak dapat digunakan</h4>
                <p class="mb-0">{{ $health['error'] ?: 'Tidak ada respons dari service.' }}</p>
            </div>
            <div class="text-muted">Pastikan service FastAPI berjalan pada URL yang diatur di <code>ML_SERVICE_URL</code>, lalu muat ulang halaman ini.</div>
        @endif
    </div>
</div>
@endsection

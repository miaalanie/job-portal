@extends('layouts.frontend')

@section('title', 'Pendaftaran Berhasil - FindTalen')

@section('content')
<div class="py-5 bg-light" style="min-height: 100vh; padding-top: 150px !important;">
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg text-center p-5 rounded-4">
                    <div class="mb-4">
                        @if($company && $company->logo)
                            <img src="{{ asset('storage/'.$company->logo) }}" height="100" class="mb-4" alt="Company Logo">
                        @else
                            <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto" style="width: 100px; height: 100px; border-radius: 50%;">
                                <i class="material-icons fs-1 mt-3">check_circle</i>
                            </div>
                        @endif
                    </div>
                    
                    <h1 class="fw-bold text-dark mb-4">Informasi</h1>
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 p-4 rounded-4 mb-5">
                        <p class="fs-5 text-dark mb-0 lh-lg">
                            Selamat pendaftaran anda berhasil, silahkan cek email anda untuk aktivasi agar anda bisa mengakses halaman admin aplikasi. 
                            <strong>Setelah berhasil login silahkan upload berkas yang diperlukan.</strong>
                        </p>
                    </div>

                    <div class="d-grid gap-3 d-sm-flex justify-content-center">
                        <a href="{{ route('login') }}" class="btn btn-theme px-5 py-3 rounded-pill fw-bold">
                            <i class="material-icons align-middle me-2">login</i> Ke Halaman Login
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary px-5 py-3 rounded-pill fw-bold border-2">
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

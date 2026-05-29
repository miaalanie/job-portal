@extends('layouts.frontend')

@section('title', 'Konfirmasi Password - ' . ($company->nama_perusahaan ?? 'FindTalen'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
<style>
    :root {
        --auth-bg-image: url('{{ asset('storage/images/auth_bg.png') }}');
    }
</style>
@endpush

@section('content')
<div class="login-section py-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="login-card row g-0">
                    <div class="col-lg-5 login-illustration d-none d-lg-flex position-relative">
                        <div class="position-relative z-index-1">
                            <i class="material-icons fs-1 text-warning mb-4">lock</i>
                            <h2 class="display-6 fw-bold text-white mb-4 lh-sm">Area Aman</h2>
                            <p class="fs-5 opacity-90 mb-5 pe-4">Untuk alasan keamanan, silakan konfirmasi password Anda sebelum melanjutkan.</p>
                        </div>
                    </div>
                    <div class="col-lg-7 login-form-container">
                        <div class="mb-5 text-center text-lg-start">
                            <h2 class="fw-bold text-dark mb-2 fs-1">Konfirmasi Password</h2>
                            <p class="text-muted fs-5">Area sensitif. Masukkan password untuk verifikasi identitas Anda.</p>
                        </div>

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf
                            <div class="mb-5">
                                <label class="form-label fw-bold text-dark mb-2 d-flex align-items-center">
                                    <i class="material-icons fs-5 me-2 text-primary-theme">key</i> Password Akun
                                </label>
                                <input type="password" name="password" id="password" 
                                       class="form-control-custom w-100" 
                                       placeholder="••••••••" required autocomplete="current-password" autofocus />
                                @error('password')
                                    <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-theme-login w-100 mt-2 shadow-sm py-3">
                                KONFIRMASI AKSES <i class="material-icons align-middle ms-2 fs-5">lock_open</i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

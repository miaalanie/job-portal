@extends('layouts.frontend')

@section('title', 'Reset Password - ' . ($company->nama_perusahaan ?? 'FindTalen'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
<style>
    :root {
        --auth-bg-image: url('{{ asset('storage/images/auth_bg.png') }}');
    }
</style>
<!-- PNotify -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.brighttheme.css" rel="stylesheet" type="text/css" />
<!-- NProgress -->
<link href="https://unpkg.com/nprogress@0.2.0/nprogress.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="login-section py-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="login-card row g-0">
                    <!-- Left: Illustration Side -->
                    <div class="col-lg-5 login-illustration d-none d-lg-flex position-relative">
                        <div class="position-relative z-index-1">
                            <div class="badge bg-white bg-opacity-20 d-inline-block px-3 py-1 rounded-pill mb-4 text-white">
                                <i class="material-icons align-middle fs-6 me-1">security</i> Keamanan Akun
                            </div>
                            <h2 class="display-6 fw-bold text-white mb-4 lh-sm">
                                Buat <br><span class="text-warning fw-extrabold">Password Baru</span>
                            </h2>
                            <p class="fs-5 opacity-90 mb-5 pe-4 pe-lg-5">Pastikan password baru Anda aman dan mudah diingat. Gunakan kombinasi karakter yang kuat.</p>
                            
                            <div class="vstack gap-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                                        <i class="material-icons fs-4 text-warning">vpn_key</i>
                                    </div>
                                    <div class="small opacity-80">Gunakan Minimal 8 Karakter</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                                        <i class="material-icons fs-4 text-info">lock_reset</i>
                                    </div>
                                    <div class="small opacity-80">Ubah Kapan Saja</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Form Section -->
                    <div class="col-lg-7 login-form-container">
                        <div class="mb-5 text-center text-lg-start">
                            <h2 class="fw-bold text-dark mb-2 fs-1">Ganti Password</h2>
                            <p class="text-muted fs-5">Silakan masukkan password baru untuk akun Anda.</p>
                        </div>

                        <form id="reset_password_form" action="{{ route('password.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark mb-2 d-flex align-items-center">
                                    <i class="material-icons fs-5 me-2 text-primary-theme">alternate_email</i> Email Terdaftar
                                </label>
                                <input type="email" name="email" id="email" 
                                       class="form-control-custom w-100" 
                                       placeholder="email@anda.com" value="{{ old('email', $email) }}" required readonly />
                                @error('email')
                                    <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark mb-2 d-flex align-items-center">
                                    <i class="material-icons fs-5 me-2 text-primary-theme">key</i> Password Baru
                                </label>
                                <input type="password" name="password" id="password" 
                                       class="form-control-custom w-100" 
                                       placeholder="••••••••" required autofocus />
                                @error('password')
                                    <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <label class="form-label fw-bold text-dark mb-2 d-flex align-items-center">
                                    <i class="material-icons fs-5 me-2 text-primary-theme">lock_outline</i> Konfirmasi Password Baru
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                       class="form-control-custom w-100" 
                                       placeholder="••••••••" required />
                                @error('password_confirmation')
                                    <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" id="submit_button" class="btn btn-theme-login w-100 mt-2 shadow-sm py-3">
                                PERBARUI PASSWORD SEKARANG <i class="material-icons align-middle ms-2 fs-5">save</i>
                            </button>

                            <div class="text-center mt-5">
                                <p class="small text-muted mb-0">Butuh bantuan teknis? <a href="#" class="text-primary-theme fw-semibold text-decoration-none">Hubungi Support</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.buttons.js"></script>
<script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>

<script>
    $(document).ready(function() {
        var form = $('#reset_password_form');
        var submitButton = $('#submit_button');

        form.on('submit', function() {
            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');
            NProgress.start();
        });
    });
</script>
@endpush

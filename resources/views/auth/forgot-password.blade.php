@extends('layouts.frontend')

@section('title', 'Lupa Password - ' . ($company->nama_perusahaan ?? 'FindTalen'))

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
                                <i class="material-icons align-middle fs-6 me-1">help_outline</i> Pemulihan Akun
                            </div>
                            <h2 class="display-6 fw-bold text-white mb-4 lh-sm">
                                Lupa <br><span class="text-warning fw-extrabold">Password?</span>
                            </h2>
                            <p class="fs-5 opacity-90 mb-5 pe-4">Jangan khawatir, kami akan membantu Anda mendapatkan kembali akses ke dashboard portal Anda.</p>
                            
                            <div class="vstack gap-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                                        <i class="material-icons fs-4 text-warning">mark_email_unread</i>
                                    </div>
                                    <div class="small opacity-80">Link Aktivasi via Email</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                                        <i class="material-icons fs-4 text-info">verified</i>
                                    </div>
                                    <div class="small opacity-80">Proses Aman & Cepat</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Form Section -->
                    <div class="col-lg-7 login-form-container">
                        <div class="mb-5 text-center text-lg-start">
                            <h2 class="fw-bold text-dark mb-2 fs-1">Reset Password</h2>
                            <p class="text-muted fs-5">Lupa password? Tidak masalah. Beritahu kami alamat email Anda dan kami akan mengirimkan link reset password (GRATIS).</p>
                        </div>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success border-0 shadow-sm rounded-4 py-3 px-4 mb-4 d-flex align-items-center">
                                <i class="material-icons me-3 fs-3">check_circle</i>
                                <div class="fw-semibold">{{ session('status') }}</div>
                            </div>
                        @endif

                        <form id="forgot_password_form" action="{{ route('password.email') }}" method="POST">
                            @csrf
                            <div class="mb-5">
                                <label class="form-label fw-bold text-dark mb-2 d-flex align-items-center">
                                    <i class="material-icons fs-5 me-2 text-primary-theme">alternate_email</i> Email Terdaftar
                                </label>
                                <input type="email" name="email" id="email" 
                                       class="form-control-custom w-100" 
                                       placeholder="Masukkan email akun Anda" value="{{ old('email') }}" required autofocus />
                                @error('email')
                                    <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" id="submit_button" class="btn btn-theme-login w-100 mt-2 shadow-sm py-3">
                                KIRIM LINK RESET PASSWORD <i class="material-icons align-middle ms-2 fs-5">send</i>
                            </button>

                            <div class="text-center mt-5">
                                <p class="small text-muted mb-0">Tiba-tiba ingat password Anda? <a href="{{ route('login') }}" class="text-primary-theme fw-semibold text-decoration-none">Kembali ke Login</a></p>
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
        var form = $('#forgot_password_form');
        var submitButton = $('#submit_button');

        form.on('submit', function() {
            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Mengirim...');
            NProgress.start();
        });
    });
</script>
@endpush

@extends('layouts.frontend')

@section('title', 'Login - ' . ($company->nama_perusahaan ?? 'FindTalen'))

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
                    <div class="col-lg-5 login-illustration d-none d-lg-flex">
                        <div class="position-relative z-index-1">
                            <div class="badge bg-white bg-opacity-20 d-inline-block px-3 py-1 rounded-pill mb-4 text-white">
                                <i class="material-icons align-middle fs-6 me-1">lock_outline</i> Akses Portal
                            </div>
                            <h2 class="display-6 fw-bold text-white mb-4 lh-sm">
                                Selamat Datang Kembali di <br><span class="text-warning fw-extrabold">{{ $company->nama_perusahaan ?? 'FindTalen' }}</span>
                            </h2>
                            <p class="fs-5 opacity-90 mb-5 pe-4">Kembali akses peluang karir terbaik dan temukan masa depan profesional Anda besama kami.</p>
                            
                            <!-- Sidebar stats or micro-info -->
                            <div class="vstack gap-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                                        <i class="material-icons fs-4 text-warning">shield</i>
                                    </div>
                                    <div class="small opacity-80">Enkripsi Data Aman</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                                        <i class="material-icons fs-4 text-info">how_to_reg</i>
                                    </div>
                                    <div class="small opacity-80">Verifikasi Profil Mudah</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Form Section -->
                    <div class="col-lg-7 login-form-container">
                        <div class="mb-5 text-center text-lg-start">
                            <h2 class="fw-bold text-dark mb-2 fs-1">Login ke Portal Anda</h2>
                            <p class="text-muted fs-5">Silakan masuk menggunakan akun terdaftar.</p>
                        </div>

                        <form id="kt_sign_in_form" action="{{ route('login.post') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark mb-2 d-flex align-items-center">
                                    <i class="material-icons fs-5 me-2 text-primary-theme">alternate_email</i> Email Pengguna
                                </label>
                                <input type="email" name="email" id="email" 
                                       class="form-control-custom w-100" 
                                       placeholder="contoh: hrd@perusahaan.com" required />
                            </div>

                            <div class="mb-4 mt-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <label class="form-label fw-bold text-dark mb-0 d-flex align-items-center">
                                        <i class="material-icons fs-5 me-2 text-primary-theme">key</i> Password Akun
                                    </label>
                                    <a href="{{ route('password.request') }}" class="text-primary-theme text-decoration-none small fw-bold">Lupa Password?</a>
                                </div>
                                <input type="password" name="password" id="password" 
                                       class="form-control-custom w-100" 
                                       placeholder="••••••••" required />
                            </div>

                            <div class="form-check mb-5 mt-3">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label text-muted small fw-medium" for="remember">
                                    Ingat sesi masuk saya di perangkat ini
                                </label>
                            </div>

                            <button type="submit" id="kt_sign_in_submit" class="btn btn-theme-login w-100 mt-2 shadow-sm">
                                MASUK KE DASHBOARD <i class="material-icons align-middle ms-2 fs-5">arrow_forward</i>
                            </button>

                            <div class="divider">
                                <span>Atau Daftar Baru</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <a href="{{ route('perusahaan.register') }}" class="btn btn-outline-danger w-100 py-3 rounded-pill fw-bold border-2 small d-flex align-items-center justify-content-center">
                                        <i class="material-icons fs-5 me-2">business</i> Perusahaan
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('pelamar.register') }}" class="btn btn-outline-success w-100 py-3 rounded-pill fw-bold border-2 small d-flex align-items-center justify-content-center">
                                        <i class="material-icons fs-5 me-2">person</i> Pelamar
                                    </a>
                                </div>
                            </div>

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
        var form = $('#kt_sign_in_form');
        var submitButton = $('#kt_sign_in_submit');

        form.on('submit', function(e) {
            e.preventDefault();
            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Memproses...');
            NProgress.start();

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(res) {
                    NProgress.done();
                    if (res.status === 'success') {
                        new PNotify({
                            title: 'Berhasil Masuk',
                            text: res.message,
                            type: 'success',
                            styling: 'brighttheme',
                            delay: 2000
                        });
                        setTimeout(() => { window.location.href = res.redirect; }, 1000);
                    }
                },
                error: function(xhr) {
                    NProgress.done();
                    submitButton.prop('disabled', false).html('MASUK KE DASHBOARD <i class="material-icons align-middle ms-2 fs-5">arrow_forward</i>');
                    var msg = 'Kredensial login tidak ditemukan atau salah.';
                    if (xhr.status === 401 || xhr.status === 403) msg = xhr.responseJSON.message;
                    else if (xhr.status === 422) msg = Object.values(xhr.responseJSON.errors)[0][0];

                    new PNotify({
                        title: 'Akses Gagal',
                        text: msg,
                        type: 'error',
                        styling: 'brighttheme',
                        delay: 3000
                    });
                }
            });
        });
    });
</script>
@endpush

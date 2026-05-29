@extends('layouts.frontend')

@section('title', 'Registrasi Pelamar - FindTalen')

@section('content')
<div class="py-5 bg-light" style="min-height: 100vh; padding-top: 40px !important;">
    <div class="container py-0">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg overflow-hidden rounded-4">
                    <div class="row g-0">
                        <!-- Left Side: Visual / Branding -->
                        <div class="col-lg-5 bg-primary-theme d-none d-lg-flex flex-column justify-content-center p-5 text-white position-relative overflow-hidden">
                            <!-- Background Decor -->
                            <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
                            
                            <div class="position-relative z-index-1 text-center">
                                <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                                    <i class="material-icons fs-1">person_add</i>
                                </div>
                                <h2 class="fw-extrabold mb-4 h1">Mulai Karir <br><span class="text-warning">Impianmu</span></h2>
                                <p class="opacity-80 mb-5">Bergabunglah dengan ribuan talenta lainnya dan temukan peluang kerja terbaik dari perusahaan-perusahaan ternama.</p>
                                
                                <div class="vstack gap-4 text-start">
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons text-warning me-3">check_circle</i>
                                        <span class="small fw-medium">Registrasi Gratis & Mudah</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons text-warning me-3">check_circle</i>
                                        <span class="small fw-medium">Event Rekrutmen Eksklusif</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons text-warning me-3">check_circle</i>
                                        <span class="small fw-medium">Update Status Lamaran Real-time</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Minimalist Form -->
                        <div class="col-lg-7 bg-white p-5">
                            <div class="mb-5 text-center text-lg-start">
                                <h1 class="fw-bold fs-2 text-dark mb-2">Registrasi Pelamar</h1>
                                <p class="text-muted">Langkah awal menuju karir sukses Anda.</p>
                            </div>

                            <form id="form_registration_pelamar" class="row g-4">
                                @csrf
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="material-icons fs-6 text-muted">person</i></span>
                                        <input type="text" name="name" class="form-control bg-light border-0 py-3 rounded-end-3" placeholder="Masukkan nama lengkap" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Alamat Email Aktif <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="material-icons fs-6 text-muted">email</i></span>
                                        <input type="email" name="email" class="form-control bg-light border-0 py-3 rounded-end-3" placeholder="email@contoh.com" required>
                                    </div>
                                    <div class="form-text fs-9 mt-2 text-muted">Email ini akan digunakan untuk aktivasi akun dan pengiriman notifikasi.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control bg-light border-0 py-3 rounded-3" placeholder="Min. 8 karakter" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control bg-light border-0 py-3 rounded-3" placeholder="Ulangi password" required>
                                </div>

                                <div class="col-12 mt-5">
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" id="terms" required>
                                        <label class="form-check-label small text-muted" for="terms">
                                            Saya menyetujui <a href="#" class="text-primary-theme">Syarat & Ketentuan</a> yang berlaku.
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-theme w-100 py-3 shadow-none rounded-pill fw-bold ls-1" id="btn_submit">
                                        DAFTAR SEKARANG
                                    </button>
                                </div>

                                <div class="col-12 text-center mt-4">
                                    <p class="text-muted small">Sudah punya akun? <a href="{{ route('login') }}" class="text-primary-theme fw-bold text-decoration-none">Masuk di sini</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#form_registration_pelamar').on('submit', function(e) {
            e.preventDefault();
            
            const btn = $('#btn_submit');
            const originalText = btn.html();
            
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> PROSES DAFTAR...');
            
            $.ajax({
                url: "{{ route('pelamar.register.post') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registrasi Berhasil!',
                        text: response.message,
                        confirmButtonText: 'Buka Email Saya',
                        confirmButtonColor: '#7f1d1d'
                    }).then(() => {
                        window.location.href = "{{ route('login') }}";
                    });
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    const error = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Registrasi Gagal',
                        text: error,
                        confirmButtonColor: '#7f1d1d'
                    });
                }
            });
        });
    });
</script>
@endpush

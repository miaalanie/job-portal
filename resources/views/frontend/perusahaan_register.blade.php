@extends('layouts.frontend')

@section('title', 'Registrasi Perusahaan - FindTalen')

@section('content')
<div class="py-5 bg-light" style="min-height: 100vh; padding-top: 40px !important;">
    <div class="container-fluid px-md-5 py-0">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="row g-0">
                        <!-- Left Side: Branding / Info -->
                        <div class="col-lg-4 bg-primary-theme d-none d-lg-flex flex-column justify-content-center p-5 text-white position-relative overflow-hidden">
                            <!-- Background Decor -->
                            <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
                            
                            <div class="position-relative z-index-1">
                                <h1 class="fw-extrabold mb-4 display-6">Mitra Strategis <br><span class="text-warning">Rekrutmen</span></h1>
                                <p class="opacity-80 mb-5 fs-5">Tingkatkan potensi bisnis Anda dengan menemukan talenta terbaik melalui platform terpercaya kami.</p>
                                
                                <div class="vstack gap-4">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                                            <i class="material-icons fs-4 mt-1">description</i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1 text-warning">Dokumen Legalitas</h6>
                                            <p class="small opacity-75 mb-0">Siapkan dokumen <strong>NPWP</strong>, <strong>NIB</strong>, dan <strong>Legalitas Resmi</strong> lainnya untuk proses verifikasi yang lancar.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-start">
                                        <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                                            <i class="material-icons fs-4 mt-1">verified_user</i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Verifikasi Akun</h6>
                                            <p class="small opacity-75 mb-0">Bangun reputasi perusahaan Anda. Akun yang terverifikasi mendapatkan prioritas tayang lowongan.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-start">
                                        <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                                            <i class="material-icons fs-4 mt-1">groups</i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Akses Database Talenta</h6>
                                            <p class="small opacity-75 mb-0">Dapatkan akses ke ribuan profil kandidat yang telah dikurasi sesuai kebutuhan industri Anda.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-start">
                                        <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                                            <i class="material-icons fs-4 mt-1">mark_as_unread</i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1 text-info">Aktivasi Cepat</h6>
                                            <p class="small opacity-75 mb-0">Cek email Anda sesaat setelah mendaftar untuk mengaktifkan akses ke Dashboard Admin.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 pt-4 border-top border-white border-opacity-20 small opacity-60">
                                    <p class="mb-0 d-flex align-items-center">
                                        <i class="material-icons fs-6 me-2">help_outline</i> Butuh bantuan pendaftaran? Hubungi Support kami di (021) 123456
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Form -->
                        <div class="col-lg-8 bg-white p-5">
                            <div class="mb-5 text-center text-lg-start">
                                <h1 class="fw-bold fs-2 text-dark mb-2">Registrasi Perusahaan</h1>
                                <p class="text-muted">Lengkapi data di bawah ini untuk memulai.</p>
                            </div>

                            <form id="form_registration" class="row g-4">
                                @csrf
                                <!-- Account Info -->
                                <div class="col-12">
                                    <h5 class="fw-bold text-primary-theme border-bottom pb-2 mb-3">Informasi Akun & Kontak</h5>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Nama Perusahaan <span class="text-danger">*</span></label>
                                    <input type="text" name="namaperusahaan" class="form-control px-3 py-2 border-light-subtle rounded-3" placeholder="Contoh: PT Teknologi Maju" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Perusahaan (Login) <span class="text-danger">*</span></label>
                                    <input type="email" name="email_perusahaan" class="form-control px-3 py-2 border-light-subtle rounded-3" placeholder="hrd@perusahaan.com" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nomor Telepon <span class="text-danger">*</span></label>
                                    <input type="text" name="telp" class="form-control px-3 py-2 border-light-subtle rounded-3" placeholder="021-XXXXX" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control px-3 py-2 border-light-subtle rounded-3" placeholder="Min. 8 karakter" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control px-3 py-2 border-light-subtle rounded-3" required>
                                </div>

                                <!-- Legality Info -->
                                <div class="col-12 mt-5">
                                    <h5 class="fw-bold text-primary-theme border-bottom pb-2 mb-3">Informasi Legalitas & Alamat</h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Bentuk Badan Usaha <span class="text-danger">*</span></label>
                                    <select name="bentuk" class="form-select px-3 py-2 border-light-subtle rounded-3" required>
                                        <option value="PT">PT (Perseroan Terbatas)</option>
                                        <option value="CV">CV (Commanditaire Vennootschap)</option>
                                        <option value="Firma">Firma</option>
                                        <option value="Yayasan">Yayasan</option>
                                        <option value="Perorangan">Perorangan</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Bidang Industri <span class="text-danger">*</span></label>
                                    <select name="idkategori" id="idkategori" class="form-select select2-generic px-3 py-2 border-light-subtle rounded-3" required>
                                        <option value="">Cari Bidang...</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">NPWP Perusahaan <span class="text-danger">*</span></label>
                                    <input type="text" name="npwp" class="form-control px-3 py-2 border-light-subtle rounded-3" placeholder="XX.XXX.XXX.X-XXX.XXX" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">NIB (Nomor Induk Berusaha) <span class="text-danger">*</span></label>
                                    <input type="text" name="nib" class="form-control px-3 py-2 border-light-subtle rounded-3" placeholder="Nomor NIB 13 Digit" required>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Alamat Lengkap Kantor <span class="text-danger">*</span></label>
                                    <textarea name="alamatlengkap" class="form-control px-3 py-2 border-light-subtle rounded-3" rows="3" required></textarea>
                                </div>

                                <style>
                                    .select2-container--default .select2-selection--single {
                                        height: 45px !important;
                                        border: 1px solid #dee2e6 !important;
                                        border-radius: 0.5rem !important;
                                        padding: 8px 12px !important;
                                    }
                                    .select2-container--default .select2-selection--single .select2-selection__arrow {
                                        height: 43px !important;
                                    }
                                    .select2-container--default .select2-selection--single .select2-selection__rendered {
                                        line-height: normal !important;
                                        padding-left: 0 !important;
                                    }
                                    .select2-dropdown {
                                        border: 1px solid #dee2e6 !important;
                                        border-radius: 0.5rem !important;
                                        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
                                    }
                                </style>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Provinsi <span class="text-danger">*</span></label>
                                    <select id="provinsi_id" class="form-select select2-location px-3 py-2 border-light-subtle rounded-3" required>
                                        <option value="">Cari Provinsi...</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kota/Kabupaten <span class="text-danger">*</span></label>
                                    <select id="kota_id" class="form-select select2-location px-3 py-2 border-light-subtle rounded-3" required disabled>
                                        <option value="">Pilih Kota...</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kecamatan <span class="text-danger">*</span></label>
                                    <select id="kecamatan_id" class="form-select select2-location px-3 py-2 border-light-subtle rounded-3" required disabled>
                                        <option value="">Pilih Kecamatan...</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kelurahan <span class="text-danger">*</span></label>
                                    <select name="idkelurahan" id="kelurahan_id" class="form-select select2-location px-3 py-2 border-light-subtle rounded-3" required disabled>
                                        <option value="">Pilih Kelurahan...</option>
                                    </select>
                                </div>

                                <!-- PIC Info -->
                                <div class="col-12 mt-5">
                                    <h5 class="fw-bold text-primary-theme border-bottom pb-2 mb-3">Informasi Pimpinan & PIC</h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Pimpinan (CEO/Director) <span class="text-danger">*</span></label>
                                    <input type="text" name="namapimpinan" class="form-control px-3 py-2 border-light-subtle rounded-3" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama PIC Recruitment <span class="text-danger">*</span></label>
                                    <input type="text" name="pic" class="form-control px-3 py-2 border-light-subtle rounded-3" required>
                                </div>

                                <div class="col-12 mt-5">
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" id="terms" required>
                                        <label class="form-check-label small text-muted" for="terms">
                                            Saya menyetujui <a href="#" class="text-primary-theme">Syarat & Ketentuan</a> yang berlaku serta menjamin keaslian seluruh data yang diberikan.
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-theme w-100 py-3 shadow-sm" id="btn_submit">
                                        <i class="material-icons align-middle me-2">send</i> REGISTER PERUSAHAAN
                                    </button>
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const RegistrationFormConfig = {
        url_kategoris: "{{ url('/perusahaan/registration/kategoris') }}",
        url_provinsis: "{{ url('/perusahaan/registration/provinsis') }}",
        url_kotas: "{{ url('/perusahaan/registration/kotas') }}",
        url_kecamatans: "{{ url('/perusahaan/registration/kecamatans') }}",
        url_kelurahans: "{{ url('/perusahaan/registration/kelurahans') }}",
        url_register_post: "{{ route('perusahaan.register.post') }}",
        url_register_success: "{{ route('perusahaan.register.success') }}",
        url_login: "{{ route('login') }}"
    };
</script>
<script src="{{ asset('js/frontend/perusahaan_register.js') }}"></script>
@endpush


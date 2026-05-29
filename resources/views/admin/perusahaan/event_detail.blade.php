@extends('layouts.admin')

@section('title', 'Detail Event & Registrasi')
@section('page_title', 'Informasi Keikutsertaan Event')

@section('content')
<div class="row g-5 g-xl-10">
    <!-- Event Banner & Detail -->
    <div class="col-xl-8">
        <div class="card card-flush shadow-sm mb-10 overflow-hidden border-0">
            <div class="card-header p-0 h-200px position-relative">
                @if($even->gambar_layout)
                    <img src="{{ asset('storage/' . $even->gambar_layout) }}" class="w-100 h-100 object-fit-cover opacity-75" alt="Banner">
                @else
                    <div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center">
                        <i class="material-icons fs-5tx text-white opacity-25">event</i>
                    </div>
                @endif
                <div class="position-absolute bottom-0 start-0 p-10 w-100 bg-gradient-dark">
                    <h1 class="fw-bold text-white mb-1">{{ $even->namaperiode }}</h1>
                    <span class="text-white opacity-75 fw-semibold"><i class="material-icons fs-7 me-1 align-middle">place</i> {{ $even->lokasi }}</span>
                </div>
            </div>
            <div class="card-body p-10">
                <div class="row mb-10">
                    <div class="col-md-3 border-end">
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-40px me-3">
                                <span class="symbol-label bg-light-danger text-danger"><i class="material-icons fs-5">calendar_today</i></span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-400 fw-bold fs-8 uppercase">PERIODE EVENT</span>
                                <span class="text-gray-800 fw-bold fs-7">{{ \Carbon\Carbon::parse($even->tanggalawal)->format('d M') }} - {{ \Carbon\Carbon::parse($even->tanggalselesai)->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 border-end ps-md-5">
                         <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-40px me-3">
                                <span class="symbol-label bg-light-success text-success"><i class="material-icons fs-5">payments</i></span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-400 fw-bold fs-8 uppercase">BIAYA REGISTRASI</span>
                                <span class="text-gray-800 fw-bold fs-7">
                                    @if($even->statuspaket == 1) Bervariasi @else Rp {{ number_format($even->biaya, 0, ',', '.') }} @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 border-end ps-md-5">
                        <div class="d-flex align-items-center mb-5">
                           <div class="symbol symbol-40px me-3">
                               <span class="symbol-label bg-light-info text-info"><i class="material-icons fs-5">groups</i></span>
                           </div>
                           <div class="d-flex flex-column">
                               <span class="text-gray-400 fw-bold fs-8 uppercase">KUOTA BOOTH</span>
                               <span class="text-gray-800 fw-bold fs-7">{{ $even->registers->count() }} / {{ $even->kuota_maksimum }} Unit</span>
                           </div>
                       </div>
                   </div>
                   <div class="col-md-3 ps-md-5">
                        <div class="d-flex align-items-center mb-5">
                           <div class="symbol symbol-40px me-3">
                               <span class="symbol-label bg-light-primary text-primary"><i class="material-icons fs-5">rule</i></span>
                           </div>
                           <div class="d-flex flex-column">
                               <span class="text-gray-400 fw-bold fs-8 uppercase">BATAS LAMAR</span>
                               <span class="text-gray-800 fw-bold fs-7">{{ $even->maksimum_apply }} Posisi/User</span>
                           </div>
                       </div>
                   </div>
                </div>

                @if($even->visi)
                <div class="bg-light-primary rounded rounded-4 p-8 mb-10 border border-primary border-dashed position-relative overflow-hidden">
                    <span class="position-absolute opacity-10 end-0 bottom-0 mb-n5 me-n5"><i class="material-icons text-primary" style="font-size: 150px;">format_quote</i></span>
                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center"><i class="material-icons me-2 fs-4">stars</i> Visi & Tujuan Event</h5>
                    <p class="fs-6 text-gray-700 italic fw-medium mb-0 position-relative z-index-1">"{{ $even->visi }}"</p>
                </div>
                @endif

                <div class="row mb-10">
                    <div class="col-md-7">
                        <h3 class="fw-bold text-gray-800 mb-4">Mengenai Event</h3>
                        <div class="text-justify lh-lg fs-6 text-gray-700 mb-8">
                            {!! nl2br($even->keterangan) !!}
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card card-dashed bg-light p-6">
                            <h5 class="fw-bold text-gray-800 mb-4"><i class="material-icons me-2 fs-5">map</i> Detail Lokasi</h5>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-gray-700 mb-1">{{ $even->lokasi }}</span>
                                <span class="fs-7 text-gray-600 mb-4">{{ $even->alamat_lengkap }}</span>
                                @if($even->latitude && $even->longitude)
                                    <a href="https://www.google.com/maps?q={{ $even->latitude }},{{ $even->longitude }}" target="_blank" class="btn btn-sm btn-light-primary w-100"><i class="material-icons fs-7 me-1">open_in_new</i> Lihat di Google Maps</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($even->sponsors->count() > 0)
                <div class="mb-10">
                    <h5 class="fw-bold text-gray-800 mb-5">Sponsor & Partner</h5>
                    <div class="d-flex flex-wrap gap-4">
                        @foreach($even->sponsors as $sponsor)
                            <div class="border rounded-pill px-4 py-2 d-flex align-items-center bg-light">
                                @if($sponsor->logo)
                                    <img src="{{ asset('storage/' . $sponsor->logo) }}" style="height: 20px;" class="me-2">
                                @else
                                    <i class="material-icons fs-7 me-2 text-danger">verified</i>
                                @endif
                                <span class="fw-bold fs-8 text-gray-700">{{ $sponsor->nama }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-6">
                    <i class="material-icons fs-2tx text-danger me-4">campaign</i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <h4 class="text-gray-900 fw-bold">Ketentuan Keikutsertaan</h4>
                            <div class="fs-6 text-gray-700">Pastikan profil perusahaan sudah lengkap sebelum mendaftar. Pendaftaran yang sudah disubmit akan diverifikasi oleh Admin.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="col-xl-4">
        <div class="card card-flush shadow-sm h-lg-100 border-0">
            <div class="card-header pt-7">
                <h3 class="card-title fw-bold text-gray-800">Formulir Pendaftaran</h3>
            </div>
            <div class="card-body">
                @if($isRegistered)
                    <div class="text-center py-10">
                        <div class="symbol symbol-100px symbol-circle mb-5 animate__animated animate__bounceIn">
                            <span class="symbol-label bg-light-success text-success">
                                <i class="material-icons fs-5tx">verified</i>
                            </span>
                        </div>
                        <h3 class="fw-bold text-gray-800 mb-2">Pendaftaran Diterima</h3>
                        <p class="text-muted fw-semibold mb-8">Pendaftaran Anda sedang dalam tahap verifikasi admin. Silakan cek menu Laporan Pendaftaran atau email Anda untuk status pembayaran.</p>
                        <a href="{{ route('admin.perusahaan.dashboard') }}" class="btn btn-secondary fw-bold w-100 rounded-pill">Kembali ke Dashboard</a>
                    </div>
                @else
                    <p class="text-muted mb-8">Silakan lengkapi opsi pendaftaran di bawah ini untuk berpartisipasi dalam event ini.</p>
                    
                    <form action="{{ route('admin.perusahaan.event.register', encrypt($even->id)) }}" method="POST" id="reg-form">
                        @csrf
                        @if($even->statuspaket == 1)
                            <div class="mb-8">
                                <label class="required fw-bold fs-6 mb-5">Opsi Paket Keikutsertaan</label>
                                @foreach($even->pakets as $paket)
                                    <div class="packet-option mb-4">
                                        <input type="radio" class="btn-check" name="idpaket" value="{{ $paket->id }}" id="paket_{{ $paket->id }}" {{ $loop->first ? 'checked' : '' }} />
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default d-flex flex-stack p-5 text-start w-100" for="paket_{{ $paket->id }}">
                                            <div class="d-flex align-items-center me-2">
                                                <div class="flex-grow-1">
                                                    <h4 class="d-flex align-items-center fs-5 fw-bold mb-1">{{ $paket->nama_paket }}</h4>
                                                    <div class="text-muted fw-semibold fs-8">{{ $paket->fasilitas ?? 'Fasilitas Standar' }}</div>
                                                </div>
                                            </div>
                                            <div class="ms-5">
                                                <span class="fs-6 fw-bold text-danger">IDR {{ number_format($paket->harga, 0, ',', '.') }}</span>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-light p-5 rounded-3 border mb-8">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-gray-600">Biaya Investasi</span>
                                    <span class="fs-4 fw-bold text-danger">IDR {{ number_format($even->biaya, 0, ',', '.') }}</span>
                                </div>
                                <div class="small text-muted mt-1">(Biaya Flat Keikutsertaan)</div>
                            </div>
                        @endif

                        <div class="mb-10">
                            <div class="form-check form-check-custom form-check-solid @error('terms') is-invalid @enderror">
                                <input class="form-check-input h-20px w-20px" type="checkbox" name="terms" value="1" id="agree_terms" required />
                                <label class="form-check-label fw-semibold text-gray-700 ms-3" for="agree_terms">
                                    Saya menyetujui <a href="#" class="text-danger fw-bold">Syarat & Ketentuan</a> keikutsertaan event ini.
                                </label>
                            </div>
                            @error('terms')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="separator separator-dashed my-8"></div>

                        <button type="button" class="btn btn-danger fw-bold w-100 py-3 rounded-pill shadow-sm" onclick="showConfirmModal()">
                            <i class="material-icons fs-5 me-2">send</i> Daftar Sekarang
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmRegModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    <i class="material-icons fs-4">close</i>
                </div>
            </div>
            <div class="modal-body pt-0 pb-10 text-center">
                <div class="symbol symbol-70px symbol-circle mb-5">
                    <span class="symbol-label bg-light-danger text-danger">
                        <i class="material-icons fs-1">help_outline</i>
                    </span>
                </div>
                <h3 class="fw-bold text-gray-800 mb-2">Konfirmasi Pendaftaran</h3>
                <p class="text-muted fw-semibold mb-8 px-5">Apakah Anda yakin ingin mendaftar di event <strong>{{ $even->namaperiode }}</strong>? Pastikan pilihan paket dan data Anda sudah benar.</p>
                
                <div class="d-flex flex-center">
                    <button type="button" class="btn btn-light-danger fw-bold me-3 px-8 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger fw-bold px-8 rounded-pill" id="confirm-submit">Ya, Daftar Sekarang</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-dark { background: linear-gradient(0deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%); }
    .object-fit-cover { object-fit: cover; }
    .btn-check:checked + label { border-color: #7f1d1d !important; background-color: rgba(127, 29, 29, 0.05) !important; }
    .btn-check:checked + label h4 { color: #7f1d1d !important; }
    .bg-light-danger { background-color: rgba(127, 29, 29, 0.08) !important; color: #7f1d1d !important; }
</style>
@endpush

@push('scripts')
<script>
    function showConfirmModal() {
        // Simple client side check before showing modal
        const terms = document.getElementById('agree_terms');
        if (!terms.checked) {
            new PNotify({
                title: 'Perhatian',
                text: 'Silakan setujui syarat dan ketentuan sebelum mendaftarkan perusahaan.',
                type: 'warning',
                styling: 'brighttheme'
            });
            return;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('confirmRegModal'));
        modal.show();
    }

    $(document).ready(function() {
        $('#confirm-submit').on('click', function() {
            NProgress.start();
            $(this).attr('disabled', true);
            $(this).html('<span class="spinner-border spinner-border-sm me-2"></span> Memproses...');
            $('#reg-form').submit();
        });

        // Backup for standard form submit
        $('#reg-form').on('submit', function() {
            NProgress.start();
            $('#confirm-submit').attr('disabled', true);
        });
    });
</script>
@endpush

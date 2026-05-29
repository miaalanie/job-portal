@extends('layouts.admin')

@section('title', 'Detail Pendaftaran Event')

@section('content')
<div class="row g-7">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Detail Pendaftaran & Pembayaran</span>
                    <span class="text-muted fw-semibold fs-7">Verifikasi proof of payment dari {{ $registration->perusahaan->nama }}</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Nama Event</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $registration->even->namaperiode }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Paket & Harga</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $registration->nama_paket_tampil }} (IDR {{ number_format($registration->biaya, 0, ',', '.') }})</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Tanggal Registrasi</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ \Carbon\Carbon::parse($registration->tanggalregister)->format('d F Y, H:i') }}</span>
                    </div>
                </div>
                <hr class="border-gray-200 my-10">
                
                @if($registration->payment)
                    <h4 class="fw-bold text-gray-800 mb-6">Informasi Pembayaran</h4>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">Bank Pengirim</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $registration->payment->bank_asal }}</span>
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">Nama Pengirim</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $registration->payment->nama_pengirim }}</span>
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">Jumlah Dibayarkan</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-success">IDR {{ number_format($registration->payment->jumlah_bayar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="row mb-10">
                        <label class="col-lg-4 fw-semibold text-muted">Bukti Transfer</label>
                        <div class="col-lg-8">
                            @php $ext = pathinfo($registration->payment->bukti_bayar, PATHINFO_EXTENSION); @endphp
                            @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                <a href="{{ asset('storage/' . $registration->payment->bukti_bayar) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $registration->payment->bukti_bayar) }}" class="img-fluid rounded border shadow-sm" style="max-height: 400px;">
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $registration->payment->bukti_bayar) }}" target="_blank" class="btn btn-light-primary fw-bold">
                                    <i class="material-icons fs-4 me-2">description</i> Lihat Dokumen PDF
                                </a>
                            @endif
                        </div>
                    </div>
                @elseif($registration->biaya == 0)
                    <div class="alert bg-light-primary d-flex flex-column flex-sm-row p-7 rounded-3 border-0 mt-5 mb-10">
                        <i class="material-icons fs-2tx text-primary me-4 mb-5 mb-sm-0">verified</i>
                        <div class="d-flex flex-column pe-0 pe-sm-10">
                            <h4 class="fw-bold text-primary fs-4 mb-1">Pendaftaran Tanpa Biaya (Free)</h4>
                            <span class="text-muted opacity-90 fs-6">Pendaftaran ini tidak memerlukan bukti pembayaran. Anda dapat langsung mengaktivasi pendaftaran ini untuk memberikan akses booth kepada perusahaan.</span>
                        </div>
                    </div>
                @endif

                <hr class="border-gray-200 my-10">
                <div class="d-flex align-items-center justify-content-between mb-6">
                    <h4 class="fw-bold text-gray-800 mb-0">Daftar Lowongan Kerja</h4>
                    <span class="badge badge-light-info fw-bold">{{ $registration->lowongans->count() }} Lowongan</span>
                </div>
                
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-3">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th>Nama Lowongan</th>
                                <th>Kategori</th>
                                <th class="text-end">Gaji / Kuota</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @forelse($registration->lowongans as $loker)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold fs-6">{{ $loker->namalowongan }}</span>
                                            <span class="text-muted fs-8">{{ $loker->kategorilokasi }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary fw-bold">{{ $loker->kategori->nama ?? '-' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold fs-7">Rp {{ number_format($loker->gaji_awal, 0, ',', '.') }}</span>
                                            <span class="text-muted fs-8">{{ $loker->kuota }} Kandidat</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-10 opacity-50">
                                        <span class="fst-italic">Belum ada lowongan yang ditambahkan untuk event ini.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-active-light-primary me-2">Kembali</a>
                @if(($registration->payment || $registration->biaya == 0) && $registration->aktivasi == 0)
                    <form action="{{ route('admin.event-registration.approve', encrypt($registration->id)) }}" method="POST" id="approve-form">
                        @csrf
                        <button type="button" class="btn btn-success fw-bold px-10" id="approve-btn">{{ $registration->biaya == 0 ? 'Aktivasi Akses (Free)' : 'Setujui & Aktivasi' }}</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-9">
                <div class="text-center mb-8">
                    <div class="symbol symbol-100px symbol-circle mb-5 border border-3 border-light">
                        @if($registration->perusahaan->logo)
                            <img src="{{ asset('storage/' . $registration->perusahaan->logo) }}" alt="image">
                        @else
                            <span class="symbol-label fs-1 text-primary bg-light-primary fw-bold">{{ substr($registration->perusahaan->nama, 0, 1) }}</span>
                        @endif
                    </div>
                    <h3 class="fw-bold text-gray-800">{{ $registration->perusahaan->nama }}</h3>
                    <span class="badge badge-light-success fw-bold px-4 py-1">Mitra Terverifikasi</span>
                </div>
                <div class="separator separator-dashed my-8"></div>
                <div class="mb-6">
                    <div class="fw-semibold text-muted mb-1">Email Perusahaan:</div>
                    <div class="fw-bold text-gray-800">{{ $registration->perusahaan->email }}</div>
                </div>
                <div class="mb-6">
                    <div class="fw-semibold text-muted mb-1">Telepon:</div>
                    <div class="fw-bold text-gray-800">{{ $registration->perusahaan->telp }}</div>
                </div>
                <div>
                    <div class="fw-semibold text-muted mb-1">Alamat:</div>
                    <div class="fw-bold text-gray-800 fs-7">{{ $registration->perusahaan->alamat }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#approve-btn').on('click', function() {
            Swal.fire({
                title: 'Aktivasi Pendaftaran?',
                text: "Anda akan menyetujui pendaftaran dan mengaktifkan akses perusahaan untuk event ini.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui & Aktifkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    $('#approve-btn').attr('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Memproses...');
                    $('#approve-form').submit();
                }
            });
        });
    });
</script>
@endpush

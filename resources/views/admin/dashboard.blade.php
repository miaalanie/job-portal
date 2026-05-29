@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Welcome Area -->
<div class="card card-flush h-lg-100 border-0 shadow-sm mb-10 overflow-hidden" style="background: linear-gradient(112.14deg, {{ $company->primary_color ?? '#7f1d1d' }} 0%, {{ $company->secondary_color ?? '#450a0a' }} 100%);">
    <div class="card-body d-flex flex-column justify-content-center p-10 position-relative">
        <h1 class="fw-bold text-white fs-2qx mb-1">Pusat Kendali {{ $company->nama_perusahaan ?? 'Platform' }}</h1>
        <span class="text-white opacity-75 fw-semibold fs-4">Halo {{ Auth::user()->name }}, monitor seluruh aktivitas platform hari ini.</span>
    </div>
</div>

<!-- Stats Rows -->
<div class="row g-5 g-xl-10 mb-10">
    <!-- Events Stats -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-flush h-100 shadow-sm border-0 border-top border-primary border-3">
            <div class="card-body d-flex flex-column justify-content-center p-6 text-center">
                <i class="material-icons fs-2tx text-primary mb-3">event</i>
                <div class="fs-2hx fw-bold text-gray-800">{{ $stats['totalEvents'] }}</div>
                <div class="text-muted fw-semibold uppercase fs-9 ls-2">TOTAL EVENT</div>
                <div class="d-flex justify-content-center gap-4 mt-3">
                    <span class="badge badge-light-success fw-bold">{{ $stats['activeEvents'] }} Aktif</span>
                    <span class="badge badge-light-danger fw-bold">{{ $stats['inactiveEvents'] }} Tutup</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Stats -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-flush h-100 shadow-sm border-0 border-top border-success border-3">
            <div class="card-body d-flex flex-column justify-content-center p-6 text-center">
                <i class="material-icons fs-2tx text-success mb-3">business</i>
                <div class="fs-2hx fw-bold text-gray-800">{{ $stats['validatedCompanies'] }}</div>
                <div class="text-muted fw-semibold uppercase fs-9 ls-2">PERUSAHAAN TERVALIDASI</div>
                <div class="text-gray-400 fs-8 mt-2 d-flex align-items-center justify-content-center">
                     <span class="bullet bullet-dot bg-warning h-6px w-6px me-2"></span> {{ $pendingCompanies->count() }} Menunggu Review
                </div>
            </div>
        </div>
    </div>

    <!-- Event Pendaftaran Stats -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-flush h-100 shadow-sm border-0 border-top border-warning border-3">
            <div class="card-body d-flex flex-column justify-content-center p-6 text-center">
                <i class="material-icons fs-2tx text-warning mb-3">shopping_cart</i>
                <div class="fs-2hx fw-bold text-gray-800">{{ $stats['pendingEventRegistrations'] }}</div>
                <div class="text-muted fw-semibold uppercase fs-9 ls-2">PENDAFTARAN EVENT TERTUNDA</div>
                <div class="text-gray-400 fs-8 mt-2">Menunggu tinjauan bukti bayar</div>
            </div>
        </div>
    </div>

    <!-- Applicant Stats -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-flush h-100 shadow-sm border-0 border-top border-info border-3">
            <div class="card-body d-flex flex-column justify-content-center p-6 text-center">
                <i class="material-icons fs-2tx text-info mb-3">people</i>
                <div class="fs-2hx fw-bold text-gray-800">{{ $stats['totalApplicants'] }}</div>
                <div class="text-muted fw-semibold uppercase fs-9 ls-2">TOTAL PELAMAR</div>
                <div class="text-gray-400 fs-8 mt-2">Database seluruh database</div>
            </div>
        </div>
    </div>

    <!-- Time/System Info -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-flush h-100 shadow-sm border-0 bg-light-info">
            <div class="card-body d-flex flex-column justify-content-center p-6">
                <div class="d-flex align-items-center mb-2">
                    <i class="material-icons text-info me-2">schedule</i>
                    <span class="fw-bold text-gray-800">{{ date('d M Y') }}</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="bullet bullet-dot bg-success h-6px w-6px me-2"></span>
                    <span class="text-gray-600 fs-8 fw-semibold">Sistem Status: Operasional</span>
                </div>
                <hr class="border-info opacity-10">
                <button class="btn btn-sm btn-info fw-bold w-100 mt-2">Server Logs</button>
            </div>
        </div>
    </div>
</div>

<!-- Pending Validations Table -->
<div class="row g-5 g-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush h-md-100 shadow-sm border-0">
            <div class="card-header pt-7 align-items-center">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800 fs-3">Verifikasi Perusahaan Baru</span>
                    <span class="text-gray-400 mt-1 fw-semibold fs-6 px-1">Tinjau profil & dokumen perusahaan yang baru bergabung</span>
                </h3>
                <div class="card-toolbar">
                    <span class="badge badge-light-warning fw-bold px-4 py-3">{{ $pendingCompanies->count() }} Permintaan Masuk</span>
                </div>
            </div>
            <div class="card-body pt-6">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-5">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-4 rounded-start">Perusahaan & PIC</th>
                                <th>Kontak</th>
                                <th>Tgl Registrasi</th>
                                <th>Status Email</th>
                                <th class="pe-4 text-end rounded-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingCompanies as $pending)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                @if($pending->perusahaan && $pending->perusahaan->logo)
                                                    <img src="{{ asset('storage/' . $pending->perusahaan->logo) }}" alt="Logo">
                                                @else
                                                    <span class="symbol-label bg-light-primary text-primary fw-bold text-uppercase">{{ substr($pending->name, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <a href="#" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">{{ $pending->perusahaan->nama ?? $pending->name }}</a>
                                                <span class="text-muted fw-semibold d-block fs-7">PIC: {{ $pending->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column mb-1">
                                            <span class="text-gray-800 fw-bold fs-7">{{ $pending->email }}</span>
                                            <span class="text-muted fw-semibold fs-8">{{ $pending->perusahaan->telp ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-gray-800 fw-bold fs-7">{{ $pending->created_at->format('d M Y, H:i') }}</span>
                                    </td>
                                    <td>
                                        @if($pending->statusaktif == 1)
                                            <span class="badge badge-light-success fs-8 fw-bold">Teraktivasi</span>
                                        @else
                                            <span class="badge badge-light-danger fs-8 fw-bold">Belum Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('admin.perusahaan.validation-detail', encrypt($pending->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Review Detail">
                                            <i class="material-icons fs-3">visibility</i>
                                        </a>
                                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm approve-direct-btn" data-id="{{ encrypt($pending->id) }}" title="Langsung Validasi">
                                            <i class="material-icons fs-3">done</i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-10 opacity-50">
                                        <i class="material-icons fs-2tx mb-3">verified</i>
                                        <div class="fw-bold">Semua Perusahaan Sudah Tervalidasi</div>
                                        <div class="text-muted">Tidak ada permintaan verifikasi baru untuk saat ini.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Event Registration Table -->
    <div class="col-xl-12 mt-10">
        <div class="card card-flush h-md-100 shadow-sm border-0">
            <div class="card-header pt-7 align-items-center">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800 fs-3">Verifikasi Pendaftaran Event</span>
                    <span class="text-gray-400 mt-1 fw-semibold fs-6 px-1">Tinjau keikutsertaan & bukti pembayaran perusahaan</span>
                </h3>
                <div class="card-toolbar">
                    <span class="badge badge-light-danger fw-bold px-4 py-3">{{ $pendingEventRegistrations->count() }} Pendaftaran Baru</span>
                </div>
            </div>
            <div class="card-body pt-6">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-5">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-4 rounded-start">Event & Perusahaan</th>
                                <th>Paket & Biaya</th>
                                <th>Status Bayar</th>
                                <th class="pe-4 text-end rounded-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingEventRegistrations as $reg)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-40px me-4">
                                                <span class="symbol-label bg-light-primary text-primary fw-bold text-uppercase">{{ substr($reg->even->namaperiode, 0, 1) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <div class="text-gray-800 fw-bold fs-6">{{ $reg->even->namaperiode }}</div>
                                                <span class="text-muted fw-semibold d-block fs-8">{{ $reg->perusahaan->nama }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column text-gray-800">
                                            <span class="fw-bold fs-7">{{ $reg->namapaket }}</span>
                                            <span class="fs-8 text-muted">IDR {{ number_format($reg->biaya, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($reg->biaya == 0)
                                            <span class="badge badge-light-success fs-8 fw-bold">FREE ACCESS</span>
                                        @elseif($reg->payment)
                                            <span class="badge badge-light-success fs-8 fw-bold">Bukti Terunggah</span>
                                        @else
                                            <span class="badge badge-light-warning fs-8 fw-bold">Menunggu Konfirmasi</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.event-registration.detail', encrypt($reg->id)) }}" class="btn btn-sm btn-info fw-bold">Review</a>
                                            <form action="{{ route('admin.event-registration.approve', encrypt($reg->id)) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success fw-bold" {{ ($reg->biaya > 0 && !$reg->payment) ? 'disabled' : '' }}>Approve</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10 opacity-50">Tidak ada pendaftaran tertunda</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.approve-direct-btn').on('click', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Validasi Langsung?',
                text: "Anda akan memvalidasi akun ini tanpa review dokumen.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00796B',
                confirmButtonText: 'Ya, Validasi!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memvalidasi dan mengirim email...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: `{{ url('/admin/company-validation') }}/${id}/approve`,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            NProgress.done();
                            Swal.close();
                            new PNotify({ title: 'Berhasil', text: res.message, type: 'success', styling: 'brighttheme' });
                            setTimeout(() => location.reload(), 1500);
                        },
                        error: function(xhr) {
                            NProgress.done();
                            Swal.close();
                            let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal memproses validasi.';
                            new PNotify({ title: 'Ditolak', text: msg, type: 'error', styling: 'brighttheme' });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Pendaftar Perusahaan')
@section('page_title', 'Data Perusahaan Terdaftar di Event')

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
<style>
    .symbol-label { font-weight: 700; }
    .table-responsive { overflow-x: auto; }
    @media (max-width: 768px) {
        .card-header .d-flex { flex-direction: column; align-items: flex-start !important; gap: 10px; }
        .w-md-300px { width: 100% !important; }
    }
    .hover-elevate-up { transition: transform 0.2s ease; }
    .hover-elevate-up:hover { transform: translateY(-5px); }
</style>
@endpush

@section('content')
<div class="d-flex flex-column gap-7">
    <!-- Search & Filter Bar -->
    <div class="card card-flush shadow-sm border-0">
        <div class="card-header pt-7">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-800 fs-3">Filter Registrasi</span>
                <span class="text-gray-400 mt-1 fw-semibold fs-7">Saring data berdasarkan event periode</span>
            </h3>
            <div class="card-toolbar">
                <form action="{{ route('admin.register') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="w-md-300px">
                        <select name="idperiode" class="form-select form-select-solid" data-control="select2" data-placeholder="Semua Event" onchange="this.form.submit()">
                            <option value="">Semua Event</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ $idperiode == $event->id ? 'selected' : '' }}>{{ $event->namaperiode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary d-flex align-items-center">
                        <i class="material-icons fs-5 me-2">filter_alt</i> Filter
                    </button>
                    <a href="{{ route('admin.register') }}" class="btn btn-light d-flex align-items-center">
                        <i class="material-icons fs-5 me-2">restart_alt</i> Reset
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Data Table -->
    <div class="card card-flush shadow-sm border-0">
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_registrations">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-250px ps-4">Informasi Mitra</th>
                            <th class="min-w-150px">Placement Event</th>
                            <th class="min-w-125px">Finansial & Paket</th>
                            <th class="min-w-125px text-center">Verifikasi</th>
                            <th class="text-end min-w-100px pe-4">Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse($registrations as $reg)
                            <tr class="hover-elevate-up">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-50px me-3">
                                            @if($reg->perusahaan && $reg->perusahaan->logo && $reg->perusahaan->logo != 'no-image')
                                                <img src="{{ asset('storage/'.$reg->perusahaan->logo) }}" alt="logo" class="object-fit-contain" />
                                            @else
                                                <div class="symbol-label bg-light-danger text-danger fs-5">{{ substr($reg->perusahaan->nama ?? 'P', 0, 1) }}</div>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold fs-6 mb-1">{{ $reg->perusahaan->nama ?? 'Data Hilang' }}</span>
                                            <span class="text-muted fs-8 d-flex align-items-center">
                                                <i class="material-icons fs-9 me-1">calendar_today</i> Daftar: {{ \Carbon\Carbon::parse($reg->tanggalregister)->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge badge-light-primary fw-bold px-3 py-2 fs-7 w-fit mb-1">{{ $reg->even->namaperiode ?? '-' }}</span>
                                        <span class="text-muted fs-8 ps-1">{{ $reg->even ? \Carbon\Carbon::parse($reg->even->tanggalawal)->format('M Y') : '' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column text-start">
                                        <span class="text-gray-800 fw-bold fs-6 mb-1">Rp {{ number_format($reg->biaya ?? 0, 0, ',', '.') }}</span>
                                        <span class="badge badge-light-success fs-9 w-fit border border-success border-opacity-10">{{ $reg->nama_paket_tampil }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center gap-2">
                                        {{-- Payment Status --}}
                                        @if($reg->biaya == 0)
                                            <span class="badge badge-light-success fw-bold px-4 py-2 text-uppercase fs-9">Free Access</span>
                                        @elseif($reg->payment)
                                            <span class="badge badge-light-info fw-bold px-4 py-2 fs-9">Lunas (Audited)</span>
                                        @else
                                            <span class="badge badge-light-warning fw-bold px-4 py-2 fs-9">Pending Payment</span>
                                        @endif

                                        {{-- Activation Toggle --}}
                                        <form action="{{ route('admin.register.toggle-aktivasi', $reg->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light-{{ $reg->aktivasi ? 'success' : 'danger' }} fw-bold w-100px py-1 fs-9">
                                                {{ $reg->aktivasi ? 'AKTIF' : 'NON-AKTIF' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.event-registration.detail', encrypt($reg->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm rounded-circle" title="Detail Audit">
                                            <i class="material-icons fs-5">visibility</i>
                                        </a>
                                        <form action="{{ route('admin.register.destroy', $reg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pendaftaran ini secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm rounded-circle" title="Hapus">
                                                <i class="material-icons fs-5 text-gray-400">delete</i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-20">
                                    <div class="d-flex flex-column align-items-center opacity-50">
                                        <i class="material-icons fs-5tx mb-3">inbox</i>
                                        <span class="fw-bold fs-4">Tidak ada data pendaftaran ditemukan</span>
                                        <span class="text-muted fs-7">Ubah filter atau pilih event lain</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#kt_table_registrations').DataTable({
            "pageLength": 10,
            "order": [],
            "language": {
                "search": "",
                "searchPlaceholder": "Cari Cepat...",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data"
            },
            "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-md-end'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
        });
    });
</script>
@endpush

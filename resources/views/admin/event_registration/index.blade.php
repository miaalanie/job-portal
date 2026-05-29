@extends('layouts.admin')

@section('title', 'Oversight: Pendaftar Event')
@section('page_title', 'Audit Pendaftaran Event')

@section('content')
<div class="row g-7">
    <!-- Quick Statistics Widgets -->
    <div class="col-xl-3 col-md-6">
        <div class="card bg-light-primary border-0 shadow-sm card-flush h-md-100">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $stats['total'] }}</span>
                    <span class="text-gray-400 pt-1 fw-semibold fs-6">Total Pendaftar</span>
                </div>
            </div>
            <div class="card-body d-flex flex-column justify-content-end pe-0">
                <span class="fs-6 fw-bold text-gray-800 me-2 mb-2 d-block">Registrasi Perusahaan</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-light-success border-0 shadow-sm card-flush h-md-100">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $stats['active'] }}</span>
                    <span class="text-gray-400 pt-1 fw-semibold fs-6">Aktivasi Selesai</span>
                </div>
            </div>
            <div class="card-body d-flex flex-column justify-content-end pe-0">
                <span class="fs-6 fw-bold text-gray-800 me-2 mb-2 d-block">Akun Terverifikasi</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-light-warning border-0 shadow-sm card-flush h-md-100">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $stats['pending'] }}</span>
                    <span class="text-gray-400 pt-1 fw-semibold fs-6">Menunggu Audit</span>
                </div>
            </div>
            <div class="card-body d-flex flex-column justify-content-end pe-0">
                <span class="fs-6 fw-bold text-gray-800 me-2 mb-2 d-block">Butuh Review</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-light-info border-0 shadow-sm card-flush h-md-100">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $stats['paid'] }}</span>
                    <span class="text-gray-400 pt-1 fw-semibold fs-6">Dana Terkumpul</span>
                </div>
            </div>
            <div class="card-body d-flex flex-column justify-content-end pe-0">
                <span class="fs-6 fw-bold text-gray-800 me-2 mb-2 d-block">Konfirmasi Bayar</span>
            </div>
        </div>
    </div>

    <!-- Active Filter Command Bar -->
    <div class="col-12 mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                @if($isAdminEvent)
                    {{-- Untuk Admin Event: scope terkunci, tampilkan info dan hidden input --}}
                    <form action="{{ route('admin.pendaftar-event') }}" method="GET" class="row g-5 align-items-center">
                        <input type="hidden" name="idperiode" value="{{ $idperiode }}">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-gray-700 fs-7">Scope Event:</label>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge badge-light-primary fw-bold fs-7 py-2 px-4">
                                    <i class="material-icons fs-6 me-1 align-middle">lock</i>
                                    {{ $events->first()?->namaperiode ?? 'Event Belum Ditentukan' }}
                                </span>
                                <span class="text-muted fs-9">Scope dikunci ke event Anda</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-gray-700 fs-7">Status Audit:</label>
                            <select name="status" class="form-select form-select-solid" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Sudah Aktif</option>
                                <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Belum Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-5 d-flex gap-2 align-self-end">
                            <button type="submit" class="btn btn-primary fw-bold flex-grow-1"><i class="material-icons fs-5 me-1">filter_alt</i> Terapkan Filter</button>
                            <a href="{{ route('admin.pendaftar-event') }}" class="btn btn-light fw-bold flex-grow-1">Reset</a>
                        </div>
                    </form>
                @else
                    {{-- Untuk Superadmin/Admin Aplikasi: dropdown penuh --}}
                    <form action="{{ route('admin.pendaftar-event') }}" method="GET" class="row g-5 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-gray-700 fs-7">Scope Event:</label>
                            <select name="idperiode" class="form-select form-select-solid" data-control="select2" data-placeholder="Filter Periode Event..." onchange="this.form.submit()">
                                <option value="">Semua Event Aktif/Lampau</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ $idperiode == $event->id ? 'selected' : '' }}>{{ $event->namaperiode }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-gray-700 fs-7">Status Audit:</label>
                            <select name="status" class="form-select form-select-solid" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Sudah Aktif</option>
                                <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Belum Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-5 d-flex gap-2 align-self-end">
                            <button type="submit" class="btn btn-primary fw-bold flex-grow-1"><i class="material-icons fs-5 me-1">filter_alt</i> Terapkan Filter</button>
                            <a href="{{ route('admin.pendaftar-event') }}" class="btn btn-light fw-bold flex-grow-1">Reset</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Audit Table -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800 fs-3">Manifest Pendaftaran Perusahaan</span>
                    <span class="text-gray-400 mt-1 fw-semibold fs-7">Seluruh data registrasi corporate per-event periode</span>
                </h3>
            </div>
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_registrations">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-250px">Detail Perusahaan</th>
                                <th class="min-w-150px">Placement Event</th>
                                <th class="min-w-125px">Finansial Audit</th>
                                <th class="min-w-125px">Verifikasi Akses</th>
                                <th class="text-end min-w-100px pe-5">Administrasi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse($registrations as $reg)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-50px me-4">
                                                @if($reg->perusahaan && $reg->perusahaan->logo && $reg->perusahaan->logo != 'no-image')
                                                    <img src="{{ asset('storage/'.$reg->perusahaan->logo) }}" alt="logo" class="object-fit-contain" />
                                                @else
                                                    <span class="symbol-label bg-light-primary text-primary fw-bold text-uppercase fs-6">{{ substr($reg->perusahaan->nama ?? 'P', 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold fs-6 mb-1">{{ $reg->perusahaan->nama ?? 'Data Hilang' }}</span>
                                                <div class="d-flex align-items-center fs-8 text-muted">
                                                    <i class="material-icons fs-9 me-1">calendar_today</i> Registered: {{ \Carbon\Carbon::parse($reg->tanggalregister)->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold fs-6">{{ $reg->even->namaperiode ?? '-' }}</span>
                                            <span class="badge badge-light-primary fs-8 w-fit mt-2">{{ $reg->nama_paket_tampil }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column align-items-start">
                                            <span class="fw-bold text-gray-800 fs-7 mb-1">Rp {{ number_format($reg->biaya ?? 0, 0, ',', '.') }}</span>
                                            @if($reg->biaya == 0)
                                                <span class="badge badge-light-success fw-bold px-4 py-3 border border-success border-opacity-20 text-uppercase">
                                                    <i class="material-icons text-success fs-9 me-1">verified</i> Free Access
                                                </span>
                                            @elseif($reg->payment)
                                                <a href="{{ route('admin.event-registration.detail', encrypt($reg->id)) }}" class="badge badge-light-success fw-bold px-4 py-3 border border-success border-opacity-20">
                                                    <i class="material-icons text-success fs-9 me-1">verified</i> PEMBAYARAN: LUNAS
                                                </a>
                                            @else
                                                <span class="badge badge-light-danger fw-bold px-4 py-3 border border-danger border-opacity-20">
                                                    <i class="material-icons text-danger fs-9 me-1">warning</i> BELUM BAYAR
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.pendaftar-event.toggle-aktivasi', $reg->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light-{{ $reg->aktivasi ? 'success' : 'warning' }} fw-bold w-125px py-2">
                                                {{ $reg->aktivasi ? 'SUDAH AKTIF' : 'PENDING' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end pe-5">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('admin.event-registration.detail', encrypt($reg->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" title="Review Detail & Transaksi">
                                                <i class="material-icons fs-5">visibility</i>
                                            </a>
                                            <form action="{{ route('admin.pendaftar-event.destroy', $reg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus seluruh dependensi pendaftaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" title="Hapus Permanen">
                                                    <i class="material-icons fs-5">delete_forever</i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-20 opacity-50">
                                        <i class="material-icons fs-3tx mb-3">history_edu</i>
                                        <div class="fw-bold fs-5 text-gray-800">Tidak ada pendaftaran ditemukan</div>
                                        <div class="text-muted fs-7">Coba sesuaikan filter atau pilih scope event lain.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#kt_table_registrations').DataTable({
            "pageLength": 10,
            "order": [],
            "language": {
                "search": "Quick Search:",
                "lengthMenu": "Show _MENU_ records",
                "zeroRecords": "No matching data found"
            }
        });
    });
</script>
@endpush
@endsection

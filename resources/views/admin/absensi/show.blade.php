@extends('layouts.admin')

@section('title', 'Detail Absensi: ' . $loker->namalowongan)
@section('page_title', 'Manifest Kehadiran Pelamar')

@section('content')
@php 
    $routePrefix = Auth::user()->hasRole('Admin Perusahaan') ? 'admin.perusahaan' : 'admin';
@endphp
<div class="row g-7">
    <!-- Summary Card -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-sm border-0 h-md-100">
            <div class="card-body p-10">
                <div class="d-flex flex-column align-items-center mb-8">
                    <div class="symbol symbol-100px symbol-circle mb-5 bg-light-primary p-3">
                         <i class="material-icons fs-5tx text-primary">groups</i>
                    </div>
                    <h3 class="fw-bold text-gray-800 text-center mb-1">{{ $loker->namalowongan }}</h3>
                    <span class="badge badge-light-primary fw-bold text-uppercase fs-8">{{ $loker->register->perusahaan->nama ?? 'Unknown' }}</span>
                </div>

                <div class="vstack gap-5 text-gray-700">
                    <div class="d-flex align-items-center">
                        <i class="material-icons fs-5 me-3 text-muted">event</i>
                        <div class="d-flex flex-column">
                            <span class="fs-8 fw-semibold text-muted">Periode Bursa Kerja</span>
                            <span class="fs-7 fw-bold">{{ $loker->register->even->namaperiode }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="material-icons fs-5 me-3 text-muted">bar_chart</i>
                        <div class="d-flex flex-column">
                            <span class="fs-8 fw-semibold text-muted">Status Partisipasi</span>
                            <div class="d-flex align-items-baseline mt-1">
                                <span class="fs-4 fw-bold me-2">{{ $loker->lamarans->filter(fn($l) => $l->kehadirans->where('statushadir', 1)->first())->count() }}</span>
                                <span class="fs-8 text-muted">dari {{ $loker->lamarans->count() }} Pelamar Terdaftar</span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-8">
                <div class="d-flex flex-column gap-3">
                    <a href="{{ route($routePrefix . '.absensi.index') }}" class="btn btn-light fw-bold w-100">Kembali ke Daftar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Applicant List & Manual Attendance Form -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-7">
                <h3 class="card-title fw-bold text-gray-800">Daftar Pelamar Terdaftar</h3>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="document.getElementById('manual-absen-form').submit()">
                        <i class="material-icons fs-6 me-1">save</i> Simpan Absensi Manual
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route($routePrefix . '.absensi.manual', encrypt($loker->id)) }}" method="POST" id="manual-absen-form">
                    @csrf
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-7 gy-4">
                            <thead>
                                <tr class="text-start text-muted fw-bold text-uppercase bg-light">
                                    <th class="ps-4 rounded-start min-w-100px">
                                        <div class="form-check form-check-sm form-check-solid">
                                            <input class="form-check-input" type="checkbox" id="checkAll" />
                                            <span class="ms-2">HADIR ?</span>
                                        </div>
                                    </th>
                                    <th class="min-w-150px">Pelamar</th>
                                    <th>Log Kehadiran</th>
                                    <th class="text-end pe-4 rounded-end">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-800">
                                @forelse($loker->lamarans as $lamaran)
                                    @php 
                                        $hadirStr = $lamaran->kehadirans->where('statushadir', 1)->first();
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <div class="form-check form-check-sm form-check-solid">
                                                <input class="form-check-input attendee-check" type="checkbox" name="presents[]" value="{{ $lamaran->id }}" {{ $hadirStr ? 'checked' : '' }} />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-35px me-3">
                                                    <span class="symbol-label bg-light-primary text-primary fw-bold">{{ substr($lamaran->pelamar->namalengkap ?? $lamaran->pelamar->nama ?? 'P', 0, 1) }}</span>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold text-gray-800 text-hover-primary fs-7">{{ $lamaran->pelamar->namalengkap ?? $lamaran->pelamar->nama }}</span>
                                                    <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
                                                        <span class="text-muted fs-9 d-flex align-items-center"><i class="material-icons fs-10 me-1">badge</i> {{ $lamaran->pelamar->noktp ?? $lamaran->pelamar->nik ?? '-' }}</span>
                                                        <span class="text-muted fs-9 d-flex align-items-center"><i class="material-icons fs-10 me-1">phone</i> {{ $lamaran->pelamar->nohp ?? '-' }}</span>
                                                    </div>
                                                    @if($lamaran->sesi)
                                                        <span class="badge badge-light-info fw-bold fs-10 py-1 px-2 mt-1 w-fit">
                                                            <i class="material-icons fs-10 me-1">schedule</i> Sesi: {{ $lamaran->sesi->nama_sesi }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($hadirStr)
                                                <div class="d-flex flex-column fs-8 text-success">
                                                    <span class="fw-bold"><i class="material-icons fs-9 me-1">access_time</i> {{ $hadirStr->jam }}</span>
                                                    <span><i class="material-icons fs-9 me-1">calendar_today</i> {{ \Carbon\Carbon::parse($hadirStr->tanggal)->format('d/m/Y') }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted fs-8 italic">- Belum Absen -</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            @if($hadirStr)
                                                <span class="badge badge-light-success fw-bold">HADIR</span>
                                            @else
                                                <span class="badge badge-light-secondary fw-bold opacity-50">TIDAK HADIR</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-10 text-muted">Belum ada pelamar di lowongan ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('checkAll').addEventListener('change', function() {
        const checks = document.querySelectorAll('.attendee-check');
        checks.forEach(c => c.checked = this.checked);
    });

    $(document).ready(function() {
        $('#manual-absen-form').submit(function() {
            NProgress.start();
        });
    });
</script>
@endpush

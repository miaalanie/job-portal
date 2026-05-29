@extends('layouts.admin')

@section('title', 'Manajemen Absensi Kehadiran')
@section('page_title', 'Absensi Kehadiran Pelamar')

@section('content')
@php 
    $isCompany = Auth::user()->hasRole('Admin Perusahaan');
    $routePrefix = $isCompany ? 'admin.perusahaan' : 'admin';
@endphp
<div class="row g-7">
    <!-- Filter Section -->
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-8">
            <div class="card-body">
                <form action="{{ route($routePrefix . '.absensi.index') }}" method="GET" class="row g-5 align-items-center">
                    <div class="col-md-{{ $isCompany ? '6' : '4' }}">
                        <label class="form-label fw-bold text-gray-700 fs-7 italic">Filter Berdasarkan Event:</label>
                        @if(isset($isAdminEvent) && $isAdminEvent)
                            <input type="hidden" name="idperiode" value="{{ $idperiode }}">
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge badge-light-primary fw-bold fs-7 py-2 px-4 border border-primary border-dashed">
                                    <i class="material-icons fs-6 me-1 align-middle">lock</i>
                                    {{ $events->first()?->namaperiode ?? 'Event Belum Ditentukan' }}
                                </span>
                            </div>
                        @else
                            <select name="idperiode" class="form-select form-select-solid" data-control="select2" onchange="this.form.submit()">
                                <option value="">Semua Event Bursa Kerja</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ $idperiode == $event->id ? 'selected' : '' }}>{{ $event->namaperiode }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    @if(!$isCompany)
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-gray-700 fs-7 italic">Filter Berdasarkan Perusahaan:</label>
                        <select name="idperusahaan" class="form-select form-select-solid" data-control="select2" onchange="this.form.submit()">
                            <option value="">Semua Perusahaan</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ $idperusahaan == $company->id ? 'selected' : '' }}>{{ $company->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-{{ $isCompany ? '6' : '4' }} d-flex gap-2 align-self-end">
                        <button type="submit" class="btn btn-primary fw-bold flex-grow-1">
                            <i class="material-icons fs-5 me-1">search</i> Cari Lowongan
                        </button>
                        <a href="{{ route($routePrefix . '.absensi.index') }}" class="btn btn-light fw-bold flex-grow-1">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Vacancy List with Presence Stats -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800 fs-3">Daftar Lowongan & Monitor Kehadiran</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Klik detail untuk mengabsenkan pelamar secara manual</span>
                </h3>
            </div>
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="attendanceTable">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-200px">Posisi Lowongan</th>
                                <th class="min-w-150px">Perusahaan / Event</th>
                                <th class="min-w-100px text-center">Pelamar</th>
                                <th class="min-w-100px text-center">Hadir</th>
                                <th class="text-end min-w-150px pe-5">Aksi & QR Code</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse($vacancies as $v)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold fs-6 mb-1">{{ $v->namalowongan }}</span>
                                            <span class="badge badge-light-info fs-8 w-fit">{{ $v->kategori->nama ?? 'Umum' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold fs-7">{{ $v->register->perusahaan->nama ?? 'N/A' }}</span>
                                            <span class="text-muted fs-8">{{ $v->register->even->namaperiode ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($v->register->even && $v->register->even->status_sesi == 1)
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                @foreach($v->register->even->sesis as $s)
                                                    <div class="fs-9 border border-secondary border-dashed rounded px-2 py-1 w-100 p-2">
                                                        <span class="text-muted text-uppercase">{{ $s->nama_sesi }} ({{ substr($s->jam_mulai, 0, 5) }} - {{ substr($s->jam_selesai, 0, 5) }}):</span>
                                                        <span class="fw-bold text-gray-800">{{ $v->lamarans->where('idsesi', $s->id)->count() }}</span>
                                                    </div>
                                                @endforeach
                                                <div class="badge badge-secondary fw-bold fs-9 mt-1 px-3">TOTAL: {{ $v->lamarans_count }}</div>
                                            </div>
                                        @else
                                            <span class="badge badge-secondary fw-bold px-3 py-1">{{ $v->lamarans_count }} Orang</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($v->register->even && $v->register->even->status_sesi == 1)
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                @foreach($v->register->even->sesis as $s)
                                                    @php 
                                                        $hadirSesi = $v->lamarans->where('idsesi', $s->id)
                                                            ->filter(fn($l) => $l->kehadirans->isNotEmpty())
                                                            ->count();
                                                    @endphp
                                                    <div class="fs-9 border border-success border-dashed rounded px-2 py-1 w-100 p-2">
                                                        <span class="text-muted text-uppercase">{{ $s->nama_sesi }} ({{ substr($s->jam_mulai, 0, 5) }} - {{ substr($s->jam_selesai, 0, 5) }}):</span>
                                                        <span class="fw-bold text-success">{{ $hadirSesi }}</span>
                                                    </div>
                                                @endforeach
                                                @php 
                                                    $totalHadir = $v->lamarans->filter(fn($l) => $l->kehadirans->isNotEmpty())->count();
                                                @endphp
                                                <div class="badge badge-light-success fw-bold fs-9 mt-1 px-3">TOTAL: {{ $totalHadir }}</div>
                                            </div>
                                        @else
                                            @php 
                                                $hadir = $v->lamarans->filter(fn($l) => $l->kehadirans->isNotEmpty())->count();
                                                $percentage = $v->lamarans_count > 0 ? round(($hadir / $v->lamarans_count) * 100) : 0;
                                            @endphp
                                            <div class="d-flex flex-column align-items-center">
                                               <span class="fw-bold text-success fs-6">{{ $hadir }}</span>
                                               <div class="progress h-4px w-50px mt-1">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%"></div>
                                               </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end pe-5">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-icon btn-light-primary btn-sm rounded-circle" 
                                                    onclick="generateAttendanceQR('{{ route('pelamar.absen', encrypt($v->id)) }}', '{{ $v->namalowongan }}', '{{ $v->register->perusahaan->nama ?? '' }}')"
                                                    title="Generate QR Scanner">
                                                <i class="material-icons fs-5">qr_code_2</i>
                                            </button>
                                            <a href="{{ route($routePrefix . '.absensi.show', encrypt($v->id)) }}" class="btn btn-sm btn-light-success fw-bold rounded-pill px-4" title="Detail & Absen Manual">
                                                <i class="material-icons fs-6 me-1">fact_check</i> Absen
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-20 opacity-50">Tidak ada data lowongan ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-450px">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    <i class="material-icons">close</i>
                </div>
            </div>
            <div class="modal-body text-center px-10 pt-0 pb-10">
                <i class="material-icons fs-3tx text-primary mb-3">touch_app</i>
                <h3 class="fw-bold text-gray-800 mb-1" id="qrLokerName">Nama Lowongan</h3>
                <p class="text-muted fs-7 mb-8" id="qrCompanyName">Nama Perusahaan</p>

                <div class="bg-light rounded-4 p-8 mb-8 d-flex flex-column align-items-center">
                    <div id="qrContainer" class="mb-5 shadow-lg rounded p-3 bg-white">
                        <img id="qrImage" src="" alt="QR Code" style="width: 250px; height: 250px;">
                    </div>
                    <span class="badge badge-light-primary fs-8 fw-bold px-4 py-2">SCAN UNTUK ABSENSI KEHADIRAN</span>
                </div>

                <div class="d-flex flex-column gap-3">
                    <button type="button" class="btn btn-primary fw-bold" onclick="downloadAbsensiQR()">Unduh QR Code</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function generateAttendanceQR(url, lokerName, companyName) {
        // Construct QR using the same API
        const qrSize = 500;
        const qrSrc = `https://api.qrserver.com/v1/create-qr-code/?size=${qrSize}x${qrSize}&data=${encodeURIComponent(url)}`;
        
        document.getElementById('qrImage').src = qrSrc;
        document.getElementById('qrLokerName').innerText = lokerName;
        document.getElementById('qrCompanyName').innerText = companyName;

        const modal = new bootstrap.Modal(document.getElementById('qrModal'));
        modal.show();
    }

    function downloadAbsensiQR() {
        const qrSrc = document.getElementById('qrImage').src;
        const loker = document.getElementById('qrLokerName').innerText;
        
        const link = document.createElement('a');
        link.href = qrSrc;
        link.download = `QR-Absensi-${loker.replace(/\s+/g, '-')}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endpush

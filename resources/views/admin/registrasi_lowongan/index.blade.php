@extends('layouts.admin')

@section('title', 'Data Registrasi Lowongan')
@section('page_title', 'Database Pelamar Aktif')

@section('content')
<div class="row g-7 mb-7">
    <!-- Filter Card -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800">Filtrasi Registrasi</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Saring pelamar berdasarkan event, perusahaan, lowongan, atau cari berdasarkan nama/NIK.</span>
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.registrasi-lowongan.index') }}" method="GET" id="filter-form">
                    <div class="row g-5">
                        <!-- Filter Event -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold fw-bolder">Berdasarkan Event</label>
                            @if(isset($isAdminEvent) && $isAdminEvent)
                                <input type="hidden" name="idperiode" value="{{ $idperiode }}">
                                <div class="d-flex align-items-center mt-2">
                                    <span class="badge badge-light-primary fw-bold fs-7 py-2 px-3 border border-primary border-dashed w-100 text-start">
                                        <i class="material-icons fs-6 me-1 align-middle">lock</i>
                                        {{ $events->first()?->namaperiode ?? 'Event Terkunci' }}
                                    </span>
                                </div>
                            @else
                                <select name="idperiode" class="form-select" data-control="select2" data-placeholder="Semua Event">
                                    <option value=""></option>
                                    @foreach($events as $e)
                                        <option value="{{ $e->id }}" {{ $idperiode == $e->id ? 'selected' : '' }}>
                                            {{ $e->namaperiode }} {{ $e->statusaktif ? '(AKTIF)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        
                        <!-- Filter Perusahaan -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold fw-bolder">Perusahaan</label>
                            <select name="idperusahaan" class="form-select" data-control="select2" data-placeholder="Semua Perusahaan">
                                <option value=""></option>
                                @foreach($companies as $c)
                                    <option value="{{ $c->id }}" {{ $idperusahaan == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Filter Lowongan -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold fw-bolder">Lowongan</label>
                            <select name="idlowongan" class="form-select" data-control="select2" data-placeholder="Semua Lowongan">
                                <option value=""></option>
                                @foreach($vacancies as $v)
                                    <option value="{{ $v->id }}" {{ $idlowongan == $v->id ? 'selected' : '' }}>{{ $v->namalowongan }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Cari Nama/NIK -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold fw-bolder">Cari Kandidat</label>
                            <div class="position-relative">
                                <i class="material-icons position-absolute top-50 start-0 translate-middle-y ms-4 text-gray-400">search</i>
                                <input type="text" name="q" class="form-control ps-12" placeholder="Nama atau NIK..." value="{{ $q }}">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-5">
                        <button type="submit" class="btn btn-primary fw-bold px-8">Terapkan Filter</button>
                        <a href="{{ route('admin.registrasi-lowongan.index') }}" class="btn btn-light fw-bold px-6">Reset All</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-800 fs-3">Seluruh Rekam Jejak Pelamar Aktif</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Rangkuman semua pencari kerja per event dan lowongan terdaftar</span>
        </h3>
        <div class="card-toolbar">
            <span class="badge badge-light-primary fw-bold border border-primary px-4 py-3">Total: {{ $lamarans->total() }} Pelamar</span>
        </div>
    </div>
    <div class="card-body pt-3">
        <div class="table-responsive">
            <table class="table table-bordered align-middle table-row-dashed fs-6 gy-5">
                <thead class="bg-light fw-bold fs-7 text-uppercase text-gray-600">
                    <tr>
                        <th class="ps-4">Kandidat & NIK</th>
                        <th>Lowongan Tujuan</th>
                        <th>Perusahaan & Event</th>
                        <th class="text-center">Sesi Ujian</th>
                        <th class="text-center">Waktu Registrasi</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @forelse($lamarans as $lamaran)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px bg-light-primary text-primary me-3 text-uppercase fw-bold text-center d-flex align-items-center justify-content-center">
                                        {{ substr($lamaran->pelamar->namalengkap ?? '?', 0, 1) }}
                                    </div>
                                    <div class="d-flex flex-column">
                                        @if($lamaran->pelamar)
                                            @php
                                                $routeAudit = Auth::user()->idperusahaan != null ? 'admin.perusahaan.pelamar.show' : 'admin.pencari-kerja.show';
                                            @endphp
                                            <a href="{{ route($routeAudit, encrypt($lamaran->pelamar->id)) }}" class="text-gray-800 text-hover-primary fw-bold fs-6 mb-0">{{ $lamaran->pelamar->namalengkap ?? 'Tanpa Nama' }}</a>
                                            <span class="text-muted fs-8">{{ $lamaran->pelamar->noktp ?? 'NIK Kosong' }}</span>
                                        @else
                                            <div class="fw-bold fs-6 text-gray-800">Pelamar Terhapus</div>
                                            <div class="text-muted fs-8 small">ID: {{ $lamaran->idpelamar }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-gray-800 fs-6">{{ $lamaran->lowongan->namalowongan ?? 'N/A' }}</div>
                                @if($lamaran->statusditerima == 1)
                                    <span class="badge badge-light-success fs-9 py-1 px-2 mt-1">Diterima</span>
                                @elseif($lamaran->statusditerima == 2)
                                    <span class="badge badge-light-danger fs-9 py-1 px-2 mt-1">Ditolak</span>
                                @else
                                    <span class="badge badge-light-warning fs-9 py-1 px-2 mt-1">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold fs-7">{{ $lamaran->lowongan->register->perusahaan->nama ?? 'N/A' }}</div>
                                <div class="text-primary italic fs-8">{{ $lamaran->lowongan->register->even->namaperiode ?? '-' }}</div>
                            </td>
                            <td class="text-center">
                                @if($lamaran->sesi)
                                    <span class="badge badge-light-info fw-bold">{{ $lamaran->sesi->nama_sesi }}</span>
                                @else
                                    <span class="text-muted fs-8">Reguler</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <div class="fw-bold fs-7">{{ \Carbon\Carbon::parse($lamaran->created_at)->format('d M Y') }}</div>
                                <div class="text-muted fs-8">{{ \Carbon\Carbon::parse($lamaran->created_at)->format('H:i') }} WIB</div>
                            </td>
                            <td class="text-end pe-4">
                                @if($lamaran->pelamar)
                                    <a href="{{ route('admin.pencari-kerja.show', encrypt($lamaran->pelamar->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" title="Audit Detail Talent">
                                        <i class="material-icons fs-5">visibility</i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-20">
                                <i class="material-icons fs-5x mb-3 text-muted">person_off</i>
                                <div class="fw-bold fs-3 text-gray-600">Pelamar Kosong</div>
                                <div class="text-muted fs-7">Belum ada pencari kerja yang melamar dengan filter ini. Coba sesuaikan filter pencarian.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($lamarans->hasPages())
        <div class="d-flex flex-stack justify-content-between pt-10">
            <div class="fs-6 fw-bold text-gray-700">
                Menampilkan {{ $lamarans->firstItem() }} s/d {{ $lamarans->lastItem() }} dari total {{ $lamarans->total() }} pendaftar
            </div>
            <ul class="pagination">
                {{ $lamarans->appends(request()->all())->links('pagination::bootstrap-5') }}
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection

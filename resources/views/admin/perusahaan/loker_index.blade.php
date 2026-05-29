@extends('layouts.admin')

@section('title', 'Data Lowongan Perusahaan')

@section('content')
<!-- Search & Filter Bar -->
<div class="card shadow-sm border-0 mb-8 mt-5">
    <div class="card-body py-6">
        <form action="{{ route('admin.perusahaan.loker.index') }}" method="GET" class="row g-5 align-items-center">
            <div class="col-md-10">
                <div class="position-relative">
                    <i class="material-icons position-absolute top-50 start-0 translate-middle-y ms-4 text-gray-400">search</i>
                    <input type="text" name="q" class="form-control ps-12 bg-light border-0" placeholder="Filter berdasarkan nama lowongan atau nama event..." value="{{ $q }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">Cari Data</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header border-0 pt-7">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-800 fs-3">Semua Lowongan Anda</span>
            <span class="text-muted mt-1 fw-semibold fs-7 px-1">Total: {{ $lowongans->total() }} Posisi di Seluruh Event</span>
        </h3>
    </div>
    <div class="card-body pt-3">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-5">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 rounded-start">Posisi / Lowongan</th>
                        <th>Event / Periode</th>
                        <th>Kategori</th>
                        <th>Pelamar</th>
                        <th>Status Event</th>
                        <th class="pe-4 text-end rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowongans as $loker)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold fs-6">{{ $loker->namalowongan }}</span>
                                    <span class="text-muted fs-8">Ditambahkan pada {{ \Carbon\Carbon::parse($loker->created_at)->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-semibold fs-7">{{ $loker->register->even->namaperiode }}</span>
                                    <span class="text-primary fs-9 italic">{{ $loker->kategorilokasi }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light-secondary text-gray-600 fw-bold">{{ $loker->kategori->nama ?? 'Umum' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-circle badge-light-primary fw-bold me-2">{{ $loker->lamarans_count }}</span>
                                    <span class="text-muted fs-8">Kandidat</span>
                                </div>
                            </td>
                            <td>
                                @if($loker->register->even->statusaktif == 1)
                                    <span class="badge badge-light-success border border-success border-dashed fw-bold px-4 py-2">
                                        <span class="bullet bullet-dot bg-success me-2"></span>Aktif
                                    </span>
                                @else
                                    <span class="badge badge-light-danger border border-danger border-dashed fw-bold px-4 py-2">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.perusahaan.loker.applicants', encrypt($loker->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" title="Kelola Pelamar">
                                        <i class="material-icons fs-5">visibility</i>
                                    </a>
                                    <a href="{{ route('admin.perusahaan.loker.edit', encrypt($loker->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm" title="Edit Lowongan">
                                        <i class="material-icons fs-5">edit</i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-20 opacity-50">
                                <i class="material-icons fs-3tx mb-3">work_off</i>
                                <div class="fw-bold fs-4">Belum Ada Lowongan</div>
                                <div class="text-muted">Data akan muncul di sini setelah Anda mempublikasikan lowongan di event resmi.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex flex-stack flex-wrap pt-10">
            <div class="fs-6 fw-bold text-gray-700">
                Showing {{ $lowongans->firstItem() }} to {{ $lowongans->lastItem() }} of {{ $lowongans->total() }} entries
            </div>
            <ul class="pagination">
                {{ $lowongans->links('pagination::bootstrap-5') }}
            </ul>
        </div>
    </div>
</div>
@endsection

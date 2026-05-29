@extends('layouts.admin')

@section('title', 'Database Pelamar')

@section('content')
<div class="card shadow-sm border-0 mb-8 mt-5">
    <div class="card-body py-6">
        <form action="{{ route('admin.perusahaan.pelamar.index') }}" method="GET" class="row g-5 align-items-center">
            <div class="col-md-3">
                <label class="form-label fw-bold fs-7 text-gray-700">Filter Event</label>
                <select name="idperiode" class="form-select form-select-solid" data-control="select2" data-placeholder="Semua Event">
                    <option value="">Semua Event</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ $idperiode == $event->id ? 'selected' : '' }}>{{ $event->namaperiode }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold fs-7 text-gray-700">Filter Lowongan</label>
                <select name="idlowongan" class="form-select form-select-solid" data-control="select2" data-placeholder="Semua Lowongan">
                    <option value="">Semua Lowongan</option>
                    @foreach($lowongans_list as $loker_item)
                        <option value="{{ $loker_item->id }}" {{ $idlowongan == $loker_item->id ? 'selected' : '' }}>{{ $loker_item->namalowongan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold fs-7 text-gray-700">Cari Nama</label>
                <div class="position-relative">
                    <i class="material-icons position-absolute top-50 start-0 translate-middle-y ms-4 text-gray-400">search</i>
                    <input type="text" name="q" class="form-control ps-12 bg-light border-0" placeholder="Nama Pelamar..." value="{{ $q }}">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2 align-self-end mt-4">
                <button type="submit" class="btn btn-primary fw-bold flex-grow-1">Filter</button>
                <a href="{{ route('admin.perusahaan.pelamar.export', request()->all()) }}" class="btn btn-success fw-bold flex-grow-1">
                    <i class="material-icons fs-5 me-1">file_download</i> Excel
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header border-0 pt-7">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-800 fs-3">Talent Pool / Database Pelamar</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Rangkuman semua pencari kerja yang tertarik pada lowongan Anda</span>
        </h3>
        <div class="card-toolbar">
            <span class="badge badge-light-primary fw-bold px-4 py-3">Total: {{ $lamarans->total() }} Pelamar</span>
        </div>
    </div>
    <div class="card-body pt-3">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-5">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 rounded-start">Kandidat</th>
                        <th>Lowongan Tujuan</th>
                        <th>Event / Lokasi</th>
                        <th>Rencana Datang</th>
                        <th>Waktu Melamar</th>
                        <th>Status</th>
                        <th class="pe-4 text-end rounded-end">Profil</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lamarans as $lamaran)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45px me-5">
                                        @if($lamaran->pelamar && $lamaran->pelamar->foto)
                                            <img src="{{ asset('storage/'.$lamaran->pelamar->foto) }}" alt="Foto">
                                        @else
                                            <span class="symbol-label bg-light-danger text-danger fw-bold text-uppercase fs-5">{{ substr($lamaran->pelamar->namalengkap ?? '?', 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-bold fs-6">{{ $lamaran->pelamar->namalengkap ?? 'Nama Tidak Tersedia' }}</span>
                                        <span class="text-muted fw-semibold fs-8">{{ $lamaran->pelamar->alamatlengkap ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold fs-7">{{ $lamaran->lowongan->namalowongan }}</span>
                                    <span class="text-muted fs-8">{{ $lamaran->lowongan->kategori->nama ?? 'Umum' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-600 fw-semibold fs-7">{{ $lamaran->lowongan->register->even->namaperiode }}</span>
                                    <span class="text-primary fs-9 italic">{{ $lamaran->lowongan->kategorilokasi }}</span>
                                </div>
                            </td>
                            <td>
                                @if($lamaran->tanggal_datang)
                                    <div class="badge badge-light-info fw-bold fs-7">
                                        <i class="material-icons fs-9 me-1">calendar_today</i> {{ \Carbon\Carbon::parse($lamaran->tanggal_datang)->format('d/m/Y') }}
                                    </div>
                                @else
                                    <span class="text-muted fs-8 font-italic">Tidak Ada Data</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-gray-600 fs-7 fw-bold">{{ \Carbon\Carbon::parse($lamaran->created_at)->format('d/m/Y') }}</div>
                                <div class="text-muted fs-9">{{ \Carbon\Carbon::parse($lamaran->created_at)->diffForHumans() }}</div>
                            </td>
                            <td>
                                @if($lamaran->statusditerima == 1)
                                    <span class="badge badge-light-success fw-bold">Diterima</span>
                                @elseif($lamaran->statusditerima == 2)
                                    <span class="badge badge-light-danger fw-bold">Ditolak</span>
                                @else
                                    <span class="badge badge-light-warning fw-bold">Menunggu</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($lamaran->pelamar)
                                    <a href="{{ route('admin.perusahaan.pelamar.show', encrypt($lamaran->pelamar->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" title="Audit Detail Talent">
                                        <i class="material-icons fs-5 text-primary">visibility</i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-20 opacity-50">
                                <i class="material-icons fs-3tx mb-3">account_circle</i>
                                <div class="fw-bold fs-4">Database Kosong</div>
                                <div class="text-muted">Belum ada kandidat yang melamar di lowongan Anda.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex flex-stack flex-wrap pt-10">
            <div class="fs-6 fw-bold text-gray-700">
                Menampilkan {{ $lamarans->firstItem() }} ke {{ $lamarans->lastItem() }} dari {{ $lamarans->total() }} kandidat
            </div>
            <ul class="pagination">
                {{ $lamarans->links('pagination::bootstrap-5') }}
            </ul>
        </div>
    </div>
</div>
@endsection

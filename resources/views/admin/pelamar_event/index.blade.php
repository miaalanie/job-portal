@extends('layouts.admin')

@section('title', 'Database Pelamar Event')
@section('page_title', 'Pelamar Pendaftar')

@section('content')
<div class="card shadow-sm border-0 mb-8 mt-5">
    <div class="card-body py-6">
        @if($isAdminEvent)
            <form action="{{ route('admin.pelamar.even') }}" method="GET" class="row g-5 align-items-center">
                <input type="hidden" name="idperiode" value="{{ $idperiode }}">
                <div class="col-md-5">
                    <label class="form-label fw-bold fs-7 text-gray-700">Scope Event:</label>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge badge-light-primary fw-bold fs-7 py-2 px-4">
                            <i class="material-icons fs-6 me-1 align-middle">lock</i>
                            {{ $events->first()?->namaperiode ?? 'Event Belum Ditentukan' }}
                        </span>
                        <span class="text-muted fs-9">Scope dikunci ke event Anda</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold fs-7 text-gray-700">Cari Nama Pelamar</label>
                    <div class="position-relative">
                        <i class="material-icons position-absolute top-50 start-0 translate-middle-y ms-4 text-gray-400">search</i>
                        <input type="text" name="q" class="form-control ps-12 bg-light border-0" placeholder="Ketik nama kandidat..." value="{{ $q }}">
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2 align-self-end mt-4">
                    <button type="submit" class="btn btn-primary fw-bold flex-grow-1"><i class="material-icons fs-5 me-1">filter_alt</i> Terapkan</button>
                    <a href="{{ route('admin.pelamar.even') }}" class="btn btn-light fw-bold">Reset</a>
                </div>
            </form>
        @else
            <form action="{{ route('admin.pelamar.even') }}" method="GET" class="row g-5 align-items-center">
                <div class="col-md-5">
                    <label class="form-label fw-bold fs-7 text-gray-700">Filter Event</label>
                    <select name="idperiode" class="form-select form-select-solid" data-control="select2" data-placeholder="Semua Event" onchange="this.form.submit()">
                        <option value="">Semua Event</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ $idperiode == $event->id ? 'selected' : '' }}>{{ $event->namaperiode }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold fs-7 text-gray-700">Cari Nama Pelamar</label>
                    <div class="position-relative">
                        <i class="material-icons position-absolute top-50 start-0 translate-middle-y ms-4 text-gray-400">search</i>
                        <input type="text" name="q" class="form-control ps-12 bg-light border-0" placeholder="Ketik nama kandidat..." value="{{ $q }}">
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2 align-self-end mt-4">
                    <button type="submit" class="btn btn-primary fw-bold flex-grow-1"><i class="material-icons fs-5 me-1">filter_alt</i> Cari</button>
                    <a href="{{ route('admin.pelamar.even') }}" class="btn btn-light fw-bold">Reset</a>
                </div>
            </form>
        @endif
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header border-0 pt-7">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-800 fs-3">Database Pelamar Event</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Seluruh histori pelamar pada lowongan per event</span>
        </h3>
        <div class="card-toolbar">
            <span class="badge badge-light-primary fw-bold px-4 py-3">Total: {{ $lamarans->total() }} Pelamar Tersaring</span>
        </div>
    </div>
    <div class="card-body pt-3">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-5">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 rounded-start">Kandidat</th>
                        <th>Perusahaan Tujuan</th>
                        <th>Lowongan Pilihan</th>
                        <th>Waktu Melamar</th>
                        <th class="pe-4 rounded-end">Event Scope</th>
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
                                        <span class="text-muted fw-semibold fs-8">{{ $lamaran->pelamar->noktp ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if(isset($lamaran->lowongan) && isset($lamaran->lowongan->register) && isset($lamaran->lowongan->register->perusahaan))
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-30px me-3">
                                        @if($lamaran->lowongan->register->perusahaan->logo && $lamaran->lowongan->register->perusahaan->logo != 'no-image')
                                            <img src="{{ asset('storage/'.$lamaran->lowongan->register->perusahaan->logo) }}" alt="logo" class="object-fit-contain" />
                                        @else
                                            <span class="symbol-label bg-light-primary text-primary fw-bold fs-8">{{ substr($lamaran->lowongan->register->perusahaan->nama, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-bold fs-7">{{ $lamaran->lowongan->register->perusahaan->nama }}</span>
                                        <span class="text-muted fs-8">{{ $lamaran->lowongan->kategorilokasi ?? '-' }}</span>
                                    </div>
                                </div>
                                @else
                                <span class="text-muted">Data Korup</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold fs-7">{{ $lamaran->lowongan->namalowongan ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="text-gray-600 fs-7 fw-bold">{{ \Carbon\Carbon::parse($lamaran->created_at)->format('d/m/Y') }}</div>
                                <div class="text-muted fs-9">{{ \Carbon\Carbon::parse($lamaran->created_at)->format('H:i') }} WIB</div>
                            </td>
                            <td class="pe-4">
                                <span class="badge badge-light fw-bold text-gray-700">
                                    <i class="material-icons fs-9 me-1 align-middle text-primary">event</i>
                                    {{ $lamaran->lowongan->register->even->namaperiode ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-20 opacity-50">
                                <i class="material-icons fs-3tx mb-3">group_add</i>
                                <div class="fw-bold fs-4">Database Kosong</div>
                                <div class="text-muted">Belum ada kandidat yang melamar pada scope pencarian event ini.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($lamarans->hasPages())
        <div class="d-flex flex-stack flex-wrap pt-10">
            <div class="fs-6 fw-bold text-gray-700">
                Menampilkan {{ $lamarans->firstItem() }} ke {{ $lamarans->lastItem() }} dari {{ $lamarans->total() }} kandidat
            </div>
            <ul class="pagination">
                {{ $lamarans->appends(request()->all())->links('pagination::bootstrap-5') }}
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection

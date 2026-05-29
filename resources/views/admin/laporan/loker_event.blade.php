@extends('layouts.admin')

@section('title', 'Laporan Lowongan per Event')
@section('page_title', 'Laporan Lowongan per Event')

@section('content')
<div class="card shadow-sm border-0 mb-8">
    <div class="card-body">
        <form action="{{ route('admin.laporan.loker-event') }}" method="GET" class="row g-5 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Pilih Event:</label>
                @if(isset($isAdminEvent) && $isAdminEvent)
                    <input type="hidden" name="idperiode" value="{{ $idperiode }}">
                    <div class="d-flex align-items-center mt-2">
                        <span class="badge badge-light-primary fw-bold fs-7 py-2 px-3 border border-primary border-dashed">
                            <i class="material-icons fs-6 me-1 align-middle">lock</i>
                            {{ $events->first()?->namaperiode ?? 'Event Terkunci' }}
                        </span>
                    </div>
                @else
                    <select name="idperiode" class="form-select" data-control="select2" required>
                        <option value="">-- Pilih Event --</option>
                        @foreach($events as $e)
                            <option value="{{ $e->id }}" {{ $idperiode == $e->id ? 'selected' : '' }}>{{ $e->namaperiode }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Pilih Perusahaan:</label>
                <select name="idperusahaan" class="form-select" data-control="select2">
                    <option value="">Semua Perusahaan</option>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}" {{ $idperusahaan == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Format Output:</label>
                <select name="format" class="form-select">
                    <option value="html" {{ request()->format == 'html' ? 'selected' : '' }}>Tampil (HTML)</option>
                    <option value="pdf" {{ request()->format == 'pdf' ? 'selected' : '' }}>Download PDF</option>
                    <option value="excel" {{ request()->format == 'excel' ? 'selected' : '' }}>Download Excel</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="material-icons fs-5 me-1">description</i> Proses Laporan
                </button>
            </div>
        </form>
    </div>
</div>

@if($data)
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="bg-light fw-bold text-uppercase fs-8">
                    <tr>
                        <th class="ps-4">Lowongan & Kategori</th>
                        <th class="text-center">Perusahaan</th>
                        <th class="text-center">Kuota</th>
                        <th class="text-center">Total Pelamar</th>
                        <th class="text-center">Rincian Per Sesi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $loker)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold fs-6 text-gray-800">{{ $loker->namalowongan }}</div>
                            <div class="text-muted fs-8">{{ $loker->kategori->nama ?? 'N/A' }}</div>
                        </td>
                        <td class="text-center fs-7">{{ $loker->register->perusahaan->nama ?? 'N/A' }}</td>
                        <td class="text-center fw-bold">{{ $loker->kuota }}</td>
                        <td class="text-center">
                            <span class="badge badge-light-primary fw-bold">{{ $loker->lamarans->count() }}</span>
                        </td>
                        <td>
                            @if($loker->register->even && $loker->register->even->status_sesi == 1)
                                <div class="d-flex flex-column gap-1">
                                    @foreach($loker->register->even->sesis as $s)
                                        @php $cnt = $loker->lamarans->where('idsesi', $s->id)->count(); @endphp
                                        <div class="fs-9 border border-dashed rounded px-2 py-1">
                                            <span class="text-muted">{{ $s->nama_sesi }}:</span>
                                            <span class="fw-bold text-gray-700">{{ $cnt }} Pelamar</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="badge badge-secondary fs-9">Tanpa Sesi</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@extends('layouts.admin')

@section('title', 'Laporan Data Diri Pelamar')
@section('page_title', 'Laporan Data Diri Pelamar')

@section('content')
<div class="card shadow-sm border-0 mb-8">
    <div class="card-body">
        <form action="{{ route('admin.laporan.pelamar-detail') }}" method="GET" class="row g-5 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-bold">Pilih Event:</label>
                @if(isset($isAdminEvent) && $isAdminEvent)
                    <input type="hidden" name="idperiode" value="{{ $idperiode }}">
                    <div class="d-flex align-items-center mt-2">
                        <span class="badge badge-light-primary fw-bold fs-7 py-2 px-3 border border-primary border-dashed">
                            <i class="material-icons fs-6 me-1 align-middle">lock</i>
                            Event Anda
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
            <div class="col-md-2">
                <label class="form-label fw-bold">Perusahaan:</label>
                <select name="idperusahaan" class="form-select" data-control="select2">
                    <option value="">Semua Perusahaan</option>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}" {{ $idperusahaan == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Lowongan:</label>
                <select name="idlowongan" class="form-select" data-control="select2">
                    <option value="">Semua Lowongan</option>
                    @foreach($vacancies as $v)
                        <option value="{{ $v->id }}" {{ $idlowongan == $v->id ? 'selected' : '' }}>{{ $v->namalowongan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Format Output:</label>
                <select name="format" class="form-select">
                    <option value="html" {{ request()->format == 'html' ? 'selected' : '' }}>Tampil (HTML)</option>
                    <option value="pdf" {{ request()->format == 'pdf' ? 'selected' : '' }}>Download PDF</option>
                    <option value="excel" {{ request()->format == 'excel' ? 'selected' : '' }}>Download Excel</option>
                </select>
            </div>
            <div class="col-md-4">
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
                        <th class="ps-4">No</th>
                        <th>NIK / No KTP</th>
                        <th>Nama Lengkap</th>
                        <th>Tempat, Tgl Lahir</th>
                        <th>No HP</th>
                        <th>Lowongan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $i => $lamaran)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="fw-bold">{{ $lamaran->pelamar->noktp }}</td>
                        <td>{{ $lamaran->pelamar->namalengkap }}</td>
                        <td>{{ $lamaran->pelamar->tempatlahir }}, {{ \Carbon\Carbon::parse($lamaran->pelamar->tanggallahir)->format('d-m-Y') }}</td>
                        <td>{{ $lamaran->pelamar->nohp }}</td>
                        <td>
                            <div class="fw-bold fs-8">{{ $lamaran->lowongan->namalowongan }}</div>
                            <div class="text-muted fs-9">{{ $lamaran->lowongan->register->perusahaan->nama ?? 'N/A' }}</div>
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

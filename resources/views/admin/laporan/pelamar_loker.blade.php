@extends('layouts.admin')

@section('title', 'Laporan Pelamar per Lowongan')
@section('page_title', 'Laporan Pelamar per Lowongan')

@section('content')
<div class="card shadow-sm border-0 mb-8">
    <div class="card-body">
        <form action="{{ route('admin.laporan.pelamar-loker') }}" method="GET" class="row g-5 align-items-end">
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
                        <th class="ps-4">Lowongan & Perusahaan</th>
                        <th class="text-center">Total Pelamar</th>
                        <th>Daftar Nama Pelamar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $loker)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-gray-800 fs-6">{{ $loker->namalowongan }}</div>
                            <div class="text-muted fs-8">{{ $loker->register->perusahaan->nama ?? 'N/A' }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light-primary fw-bold">{{ $loker->lamarans->count() }} Orang</span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($loker->lamarans as $lamaran)
                                    <span class="badge badge-secondary fs-8">{{ $lamaran->pelamar->nama }} 
                                        @if($lamaran->sesi) ({{ $lamaran->sesi->nama_sesi }}) @endif
                                    </span>
                                @empty
                                    <span class="text-muted fs-8 italic">Belum ada pelamar</span>
                                @endforelse
                            </div>
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

@extends('layouts.admin')

@section('title', 'Detail Event')
@section('page_title', 'Informasi Detail Event')

@section('content')
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <!-- Event Details -->
    <div class="col-xl-8">
        <div class="card shadow-sm mb-5">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bold text-gray-800">{{ $event->namaperiode }}</h3>
                </div>
                <div class="card-toolbar">
                    <a href="{{ route('admin.event.edit', $event->id) }}" class="btn btn-sm btn-light-warning me-2">
                        <i class="material-icons fs-5 me-1">edit</i> Edit
                    </a>
                    <a href="{{ route('admin.event') }}" class="btn btn-sm btn-light-primary">
                        <i class="material-icons fs-5 me-1">arrow_back</i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row mb-7">
                    <div class="col-md-4">
                        @if($event->gambar)
                            <img src="{{ asset('storage/' . $event->gambar) }}" class="img-fluid rounded shadow-sm" alt="Poster">
                        @else
                            <div class="bg-light rounded d-flex flex-center h-200px">
                                <span class="text-muted">No Poster</span>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        @if($event->visi)
                        <div class="mb-5">
                            <h4 class="fw-bold mb-2">Visi Event:</h4>
                            <p class="text-gray-600 italic">"{{ $event->visi }}"</p>
                        </div>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-sm-4 fw-bold">Waktu Pelaksanaan:</div>
                            <div class="col-sm-8 text-gray-700">
                                {{ \Carbon\Carbon::parse($event->tanggalawal)->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($event->tanggalselesai)->isoFormat('D MMMM Y') }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4 fw-bold">Lokasi:</div>
                            <div class="col-sm-8 text-gray-700">
                                <strong>{{ $event->lokasi }}</strong><br>
                                <span class="fs-7 text-muted">{{ $event->alamat_lengkap }}</span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4 fw-bold">Status:</div>
                            <div class="col-sm-8">
                                <span class="badge badge-{{ $event->statusaktif ? 'success' : 'danger' }}">
                                    {{ $event->statusaktif ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4 fw-bold">Kapasitas:</div>
                            <div class="col-sm-8 text-gray-700">
                                Maks. Quota: <strong>{{ $event->kuota_maksimum > 0 ? $event->kuota_maksimum : 'Tidak Terbatas' }}</strong><br>
                                Maks. Apply: <strong>{{ $event->maksimum_apply > 0 ? $event->maksimum_apply : 'Tidak Terbatas' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                @if($event->keterangan)
                <div class="mb-7">
                    <h4 class="fw-bold mb-3">Deskripsi / Keterangan:</h4>
                    <div class="p-4 bg-light rounded text-gray-700">
                        {!! nl2br(e($event->keterangan)) !!}
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($event->status_sesi)
        <div class="card shadow-sm">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bold">Manajemen Sesi</h3>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-150px">Nama Sesi</th>
                                <th class="min-w-140px">Waktu</th>
                                <th class="min-w-100px text-center">Kuota Sesi</th>
                                <th class="min-w-120px text-center">Pelamar Terdaftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['applicants_per_sesi'] as $s)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-45px me-5">
                                            <span class="symbol-label bg-light-primary">
                                                <i class="material-icons text-primary">schedule</i>
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-dark fw-bold text-hover-primary fs-6">{{ $s['nama_sesi'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @foreach($event->sesis as $es)
                                        @if($es->nama_sesi == $s['nama_sesi'])
                                            <span class="text-muted fw-bold d-block fs-7">{{ \Carbon\Carbon::parse($es->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($es->jam_selesai)->format('H:i') }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @foreach($event->sesis as $es)
                                        @if($es->nama_sesi == $s['nama_sesi'])
                                            <span class="badge badge-light-info fs-7 fw-bold">{{ $es->kuota }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    <span class="text-dark fw-bold fs-6">{{ $s['count'] }} Pelamar</span>
                                    <div class="progress h-6px w-100 mt-2">
                                        @php
                                            $kuota_sesi = 1;
                                            foreach($event->sesis as $es) {
                                                if($es->nama_sesi == $s['nama_sesi']) $kuota_sesi = $es->kuota;
                                            }
                                            $perc = ($s['count'] / ($kuota_sesi > 0 ? $kuota_sesi : 1)) * 100;
                                        @endphp
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min($perc, 100) }}%"></div>
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
    </div>

    <!-- Statistics -->
    <div class="col-xl-4">
        <!-- Perusahaan -->
        <div class="card card-flush h-md-33 mb-5 shadow-sm" style="background-color: #F1416C">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $stats['total_perusahaan'] }}</span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Perusahaan Terdaftar (Aktif)</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0 pb-5">
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                        <span class="fw-bold text-white fs-6">Tingkat Partisipasi</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lowongan -->
        <div class="card card-flush h-md-33 mb-5 shadow-sm" style="background-color: #009EF7">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $stats['total_lowongan'] }}</span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Lowongan Kerja</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0 pb-5">
                <i class="material-icons text-white opacity-25 fs-5x position-absolute end-0 bottom-0 m-5">work_outline</i>
            </div>
        </div>

        <!-- Pelamar -->
        <div class="card card-flush h-md-33 shadow-sm" style="background-color: #50CD89">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $stats['total_pelamar'] }}</span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Pelamar (Applied)</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0 pb-5">
                 <i class="material-icons text-white opacity-25 fs-5x position-absolute end-0 bottom-0 m-5">groups</i>
            </div>
        </div>
        
        @if($event->gambar_layout)
        <div class="card shadow-sm mt-5">
            <div class="card-header border-0 pt-6">
                <div class="card-title"><h4 class="fw-bold">Layout Event</h4></div>
            </div>
            <div class="card-body">
                <img src="{{ asset('storage/' . $event->gambar_layout) }}" class="img-fluid rounded border shadow-sm" alt="Layout">
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

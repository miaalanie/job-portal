@extends('layouts.admin')

@section('title', 'Pelamar: ' . $loker->namalowongan)

@section('content')
<div class="row g-7">
    <!-- Header Summary Card -->
    <div class="col-lg-12">
        <div class="card shadow-sm border-0 mb-8 overflow-hidden">
            <div class="card-body p-0">
                <div class="bg-light-primary p-9">
                    <div class="d-flex flex-stack mb-5">
                        <div class="d-flex align-items-center">
                            <a href="{{ url()->previous() }}" class="btn btn-icon btn-sm btn-light-primary me-5" title="Kembali">
                                <i class="material-icons fs-5">arrow_back</i>
                            </a>
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <h3 class="fw-bold text-gray-800 me-3 mb-0">{{ $loker->namalowongan }}</h3>
                                    <span class="badge badge-light-primary fw-bold">{{ $loker->kategori->nama ?? 'Umum' }}</span>
                                </div>
                                <div class="fw-semibold text-gray-600">Event: {{ $loker->register->even->namaperiode }}</div>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end">
                            <div class="fs-2hx fw-bold text-gray-800">{{ $loker->lamarans_count }}</div>
                            <div class="fw-bold text-muted text-uppercase fs-7">Total Pelamar</div>
                        </div>
                    </div>

                    <div class="separator separator-dashed border-gray-400 mb-5"></div>

                    <div class="row g-5">
                        <div class="col-md-3">
                            <div class="fw-semibold text-gray-400 fs-8 text-uppercase">Penempatan</div>
                            <div class="fw-bold text-gray-800">{{ $loker->kategorilokasi }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="fw-semibold text-gray-400 fs-8 text-uppercase">Kuota Sisa</div>
                            <div class="fw-bold text-gray-800">{{ $loker->kuota ?? 'Tak Terbatas' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold text-gray-400 fs-8 text-uppercase">Tahapan Gaji</div>
                            <div class="fw-bold text-gray-800">
                                @if($loker->gaji_awal && $loker->gaji_akhir)
                                Rp {{ number_format($loker->gaji_awal, 0, ',', '.') }} - Rp {{ number_format($loker->gaji_akhir, 0, ',', '.') }}
                                @else
                                Kompetitif / Negosiasi
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-9 border-top border-gray-200">
                    <h5 class="fw-bold text-gray-800 mb-4">Deskripsi & Persyaratan:</h5>
                    <div class="text-gray-600 fs-6 vacancy-desc-audit">
                        {!! $loker->deskripsi !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Applicants List -->
    <div class="col-lg-12">
        @include('admin.perusahaan.loker_applicants_section')
    </div>
</div>
@endsection
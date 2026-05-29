@extends('layouts.admin')

@section('title', 'Audit Perusahaan: ' . $perusahaan->nama)

@section('content')
<div class="row g-7">
    <!-- Company Profile Header -->
    <div class="col-12">
        <div class="card mb-5 mb-xl-10 shadow-sm border-0">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="me-7 mb-4">
                        <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative border border-4 border-light">
                            @if($perusahaan->logo && $perusahaan->logo != 'no-image')
                                <img src="{{ asset('storage/'.$perusahaan->logo) }}" alt="image">
                            @else
                                <span class="symbol-label fs-1 text-primary bg-light-primary fw-bold">{{ substr($perusahaan->nama, 0, 1) }}</span>
                            @endif
                            <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-gray-900 fs-2 fw-bold me-1">{{ $perusahaan->nama }}</span>
                                    @if($perusahaan->user && $perusahaan->user->statusvalidasi == 1)
                                        <i class="material-icons text-primary fs-3" title="Terverifikasi">verified</i>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                    <span class="d-flex align-items-center text-gray-400 me-5 mb-2">
                                        <i class="material-icons fs-5 me-1">category</i> {{ $perusahaan->kategori->nama ?? 'Umum' }}
                                    </span>
                                    <span class="d-flex align-items-center text-gray-400 me-5 mb-2">
                                        <i class="material-icons fs-5 me-1">location_on</i> {{ $perusahaan->alamatlengkap ?? '-' }}
                                    </span>
                                    <span class="d-flex align-items-center text-gray-400 mb-2">
                                        <i class="material-icons fs-5 me-1">email</i> {{ $perusahaan->email ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex my-4">
                                <a href="mailto:{{ $perusahaan->email }}" class="btn btn-sm btn-light-primary me-2 fw-bold"><i class="material-icons fs-5 me-1">mail</i> Chat Admin</a>
                                <a href="http://{{ $perusahaan->website }}" target="_blank" class="btn btn-sm btn-primary fw-bold"><i class="material-icons fs-5 me-1">public</i> Website</a>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap flex-stack">
                            <div class="d-flex flex-column flex-grow-1 pe-8">
                                <div class="d-flex flex-wrap">
                                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="fs-2 fw-bold">{{ $perusahaan->registers->count() }}</div>
                                        </div>
                                        <div class="fw-semibold fs-6 text-gray-400">Total Event</div>
                                    </div>
                                    @php 
                                        $totalJobs = $perusahaan->registers->flatMap->lowongans->count();
                                    @endphp
                                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="fs-2 fw-bold">{{ $totalJobs }}</div>
                                        </div>
                                        <div class="fw-semibold fs-6 text-gray-400">Lowongan Diposting</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5 active" data-bs-toggle="tab" href="#kt_tab_profile">Profil</a>
                    </li>
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#kt_tab_events">Riwayat Event</a>
                    </li>
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#kt_tab_vacancies">Daftar Lowongan</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="col-12 mt-4">
        <div class="tab-content" id="myTabContent">
            <!-- Profile Info -->
            <div class="tab-pane fade show active" id="kt_tab_profile" role="tabpanel">
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title fw-bold text-gray-800">Detail Profil Perusahaan</h3>
                    </div>
                    <div class="card-body py-4">
                        <div class="row mb-7">
                            <label class="col-lg-4 fw-semibold text-muted">Visi & Misi / Deskripsi Umum</label>
                            <div class="col-lg-8">
                                <span class="text-gray-800 fs-7 italic">{{ $perusahaan->gambaranumum ?? 'Tidak ada deskripsi.' }}</span>
                            </div>
                        </div>
                        <div class="row mb-7">
                            <label class="col-lg-4 fw-semibold text-muted">Identitas Bisnis (NIB / NPWP)</label>
                            <div class="col-lg-8">
                                <span class="fw-bold text-gray-800 fs-7">{{ $perusahaan->nib ?? '-' }} / {{ $perusahaan->npwp ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="row mb-7">
                            <label class="col-lg-4 fw-semibold text-muted">Person In Charge (PIC)</label>
                            <div class="col-lg-8">
                                <span class="fw-bold fs-6 text-gray-800">{{ $perusahaan->pic ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="row mb-7">
                            <label class="col-lg-4 fw-semibold text-muted">Jumlah Karyawan</label>
                            <div class="col-lg-8">
                                <span class="fw-bold fs-6 text-gray-800">{{ $perusahaan->jumlah_karyawan ?? '0' }} Orang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event History -->
            <div class="tab-pane fade" id="kt_tab_events" role="tabpanel">
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title fw-bold text-gray-800">Riwayat Keikutsertaan Event</h3>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-3">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>Nama Event</th>
                                        <th>Paket Tier</th>
                                        <th>Tanggal Daftar</th>
                                        <th class="text-center">Status Akses</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @forelse($perusahaan->registers as $reg)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fw-bold fs-6">{{ $reg->even->namaperiode ?? '-' }}</span>
                                                    <span class="text-muted fs-8">{{ $reg->even->lokasi ?? 'Online' }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-primary fw-bold px-3 py-1">{{ $reg->namapaket }}</span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($reg->tanggalregister)->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                @if($reg->aktivasi)
                                                    <span class="badge badge-light-success fw-bold px-3 py-1">SUDAH AKTIF</span>
                                                @else
                                                    <span class="badge badge-light-warning fw-bold px-3 py-1">PENDING</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-10 opacity-50">Belum pernah mendaftar di event manapun.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vacancy Manifest -->
            <div class="tab-pane fade" id="kt_tab_vacancies" role="tabpanel">
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title fw-bold text-gray-800">Manifest Lowongan Pekerjaan</h3>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-3">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>Posisi Lowongan</th>
                                        <th>Nama Event (Placement)</th>
                                        <th>Kategori Industri</th>
                                        <th class="text-end">Gaji Min / Kuota</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @php $hasVacancies = false; @endphp
                                    @foreach($perusahaan->registers as $reg)
                                        @foreach($reg->lowongans as $loker)
                                            @php $hasVacancies = true; @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-gray-800 fw-bold fs-6">{{ $loker->namalowongan }}</span>
                                                        <span class="text-muted fs-8">{{ $loker->kategorilokasi }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light-primary fw-bold">{{ $reg->even->namaperiode ?? 'External' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light-info fw-bold">{{ $loker->kategori->nama ?? '-' }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-gray-800 fw-bold fs-7">Rp {{ number_format($loker->gaji_min, 0, ',', '.') }}</span>
                                                        <span class="text-muted fs-8">{{ $loker->kuota }} Kuota</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    
                                    @if(!$hasVacancies)
                                        <tr>
                                            <td colspan="4" class="text-center py-10 opacity-50">Belum ada lowongan yang diposting.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

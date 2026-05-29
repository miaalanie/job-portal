@extends('layouts.admin')

@section('title', 'Audit Lowongan: ' . $vacancy->namalowongan)

@section('content')
<div class="row g-7">
    <!-- Vacancy Overview Header -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body pt-15 pb-9 text-center">
                <div class="symbol symbol-100px symbol-circle mb-5 border border-4 border-light">
                    @php $logo = $vacancy->register->perusahaan->logo; @endphp
                    @if($logo && $logo != 'no-image')
                        <img src="{{ asset('storage/'.$logo) }}" alt="image">
                    @else
                        <span class="symbol-label fs-1 text-primary bg-light-primary fw-bold">{{ substr($vacancy->namalowongan, 0, 1) }}</span>
                    @endif
                </div>
                <h3 class="fw-bold text-gray-800 mb-1">{{ $vacancy->namalowongan }}</h3>
                <span class="text-muted fw-bold d-block mb-3 fs-6">{{ $vacancy->register->perusahaan->nama }}</span>
                <span class="badge badge-light-primary fw-bold px-4 py-3 mb-5">{{ $vacancy->kategori->nama ?? 'Umum' }}</span>
                
                <div class="separator separator-dashed my-8"></div>
                
                <div class="d-flex flex-stack justify-content-around text-center px-4">
                    <div class="border border-gray-300 border-dashed rounded min-w-100px py-3 px-4 mb-3">
                        <div class="fs-4 fw-bold text-gray-800">{{ $vacancy->lamarans->count() }}</div>
                        <div class="fw-semibold text-gray-400 fs-8">TOTAL PELAMAR</div>
                    </div>
                    <div class="border border-gray-300 border-dashed rounded min-w-100px py-3 px-4 mb-3">
                        <div class="fs-4 fw-bold text-gray-800">{{ $vacancy->kuota }}</div>
                        <div class="fw-semibold text-gray-400 fs-8">KUOTA POSISI</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Placement Info -->
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header border-0 min-h-50px py-3">
                <h3 class="card-title fw-bold text-gray-800 fs-4">Penempatan Event</h3>
            </div>
            <div class="card-body p-9 pt-0">
                <div class="d-flex align-items-center mb-5">
                    <i class="material-icons fs-2 text-primary me-4">event</i>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fw-bold fs-7">{{ $vacancy->register->even->namaperiode }}</span>
                        <span class="text-muted fs-8">Periode: {{ \Carbon\Carbon::parse($vacancy->register->even->tanggalawal)->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <i class="material-icons fs-2 text-primary me-4">location_on</i>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fw-bold fs-7">{{ $vacancy->register->even->lokasi }}</span>
                        <span class="text-muted fs-8">{{ $vacancy->kategorilokasi }} (Kategori Lokal)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Applicant Manifest -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">Informasi Detail Pekerjaan</span>
                </h3>
                <div class="card-toolbar">
                    <span class="badge badge-light-{{ $vacancy->status == 1 ? 'success' : 'danger' }} fw-bold px-4 py-2 text-uppercase">
                        STATUS: {{ $vacancy->status == 1 ? 'AKTIF' : 'CLOSED' }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-10">
                    <h5 class="fw-bold text-gray-800 mb-4">Ringkasan Gaji & Penawaran</h5>
                    <div class="row g-5">
                        <div class="col-sm-6">
                            <div class="bg-light-success p-5 rounded d-flex align-items-center">
                                <i class="material-icons text-success fs-2hx me-4">payments</i>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold fs-6 text-gray-800">Rp {{ number_format($vacancy->gaji_awal, 0, ',', '.') }} - {{ number_format($vacancy->gaji_akhir, 0, ',', '.') }}</span>
                                    <span class="text-muted fs-9 fw-bold">ESTIMASI GAJI KOMPETITIF</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-10">
                    <h5 class="fw-bold text-gray-800 mb-4">Deskripsi & Syarat Pekerjaan</h5>
                    <div class="fs-7 text-gray-700 bg-light p-6 rounded italic">
                        {!! $vacancy->deskripsi !!}
                    </div>
                </div>

                <div class="separator separator-dashed my-10"></div>

                <h5 class="fw-bold text-gray-800 mb-6">Daftar Pelamar (Kandidat)</h5>
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-3">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th>Calon Kandidat</th>
                                <th>NIK & Kontak</th>
                                <th>Gender</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse($vacancy->lamarans as $lamaran)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-35px symbol-circle me-3">
                                                @if($lamaran->pelamar->foto && $lamaran->pelamar->foto != 'no-image')
                                                    <img src="{{ asset('storage/'.$lamaran->pelamar->foto) }}" alt="photo" />
                                                @else
                                                    <span class="symbol-label bg-light-primary text-primary fw-bold text-uppercase fs-8">{{ substr($lamaran->pelamar->namalengkap, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold fs-7">{{ $lamaran->pelamar->namalengkap }}</span>
                                                <span class="text-muted fs-9">{{ $lamaran->pelamar->tempatlahir }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fs-7 fw-bold text-gray-800">{{ $lamaran->pelamar->noktp }}</span>
                                            <span class="text-muted fs-9">{{ $lamaran->pelamar->user->email ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $lamaran->pelamar->jeniskelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.pencari-kerja.show', encrypt($lamaran->pelamar->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" title="Audit Profil Talent">
                                            <i class="material-icons fs-5">visibility</i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10 opacity-50 fst-italic">Belum ada pelamar untuk posisi ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('admin.lowongan-kerja.index') }}" class="btn btn-light btn-active-light-primary me-2">Kembali Ke Daftar</a>
            </div>
        </div>
    </div>
</div>
@endsection

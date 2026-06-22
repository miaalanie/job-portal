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
                    <h5 class="fw-bold text-gray-800 mb-4">Persyaratan Pelamar:</h5>

                    <div class="row mb-6 gx-0">
                        <div class="col-md-3 d-flex align-items-center px-4 py-3 border-end">
                            <div class="symbol symbol-40px me-3">
                                <span class="symbol-label bg-light-primary">
                                    <i class="material-icons fs-4 text-primary">wc</i>
                                </span>
                            </div>
                            <div>
                                <div class="text-muted fs-8">Preferensi Gender</div>
                                <div class="fw-bold fs-6">{{ $loker->preferensi_gender ?? 'Semua' }}</div>
                            </div>
                        </div>

                        <div class="col-md-3 d-flex align-items-center px-4 py-3 border-end">
                            <div class="symbol symbol-40px me-3">
                                <span class="symbol-label bg-light-warning">
                                    <i class="material-icons fs-4 text-warning">school</i>
                                </span>
                            </div>
                            <div>
                                <div class="text-muted fs-8">Pendidikan Minimal</div>
                                <div class="fw-bold fs-6">{{ $pendidikan[$loker->minimal_pendidikan] ?? 'Tidak ada syarat' }}</div>
                            </div>
                        </div>

                        <div class="col-md-3 d-flex align-items-center px-4 py-3 border-end">
                            <div class="symbol symbol-40px me-3">
                                <span class="symbol-label bg-light-success">
                                    <i class="material-icons fs-4 text-success">cake</i>
                                </span>
                            </div>
                            <div>
                                <div class="text-muted fs-8">Usia</div>
                                <div class="fw-bold fs-6">
                                    @if($loker->usia_min || $loker->usia_max)
                                    {{ $loker->usia_min ?? '-' }} - {{ $loker->usia_max ?? '-' }} thn
                                    @else
                                    Tidak ada syarat
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 d-flex align-items-center px-4 py-3">
                            <div class="symbol symbol-40px me-3">
                                <span class="symbol-label bg-light-info">
                                    <i class="material-icons fs-4 text-info">work_history</i>
                                </span>
                            </div>
                            <div>
                                <div class="text-muted fs-8">Pengalaman Minimal</div>
                                <div class="fw-bold fs-6">
                                    @php $exp = $loker->minimal_pengalaman_bulan ?? 0; @endphp
                                    @if($exp == 0)
                                    Fresh Graduate
                                    @else
                                    {{ floor($exp / 12) }} thn {{ $exp % 12 }} bln
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="separator separator-dashed mb-6"></div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="d-flex align-items-center text-muted fs-7 mb-3">
                                <i class="material-icons fs-6 me-1">school</i> Jurusan yang Diterima
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($loker->jurusans as $j)
                                <span class="badge badge-light-primary fs-8 py-2 px-3">{{ $j->jurusan->namajurusan ?? '-' }}</span>
                                @empty
                                <span class="text-muted fs-7">Semua jurusan</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="d-flex align-items-center text-muted fs-7 mb-3">
                                <i class="material-icons fs-6 me-1">build</i> Skill yang Dibutuhkan
                            </div>
                            <div class="d-flex flex-wrap gap-2" id="skillBadgeWrap">
                                @forelse($loker->skills as $i => $s)
                                <span class="badge badge-light-info fs-8 py-2 px-3 {{ $i >= 6 ? 'd-none skill-extra' : '' }}">
                                    {{ $s->skill->namaskill ?? '-' }}
                                </span>
                                @empty
                                <span class="text-muted fs-7">Tidak ada syarat khusus</span>
                                @endforelse

                                @if($loker->skills->count() > 6)
                                <span class="badge badge-light fs-8 py-2 px-3 cursor-pointer" id="skillToggleBtn"
                                    onclick="document.querySelectorAll('.skill-extra').forEach(e => e.classList.toggle('d-none')); this.classList.add('d-none');">
                                    +{{ $loker->skills->count() - 6 }} lainnya
                                </span>
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
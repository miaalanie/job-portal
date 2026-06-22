@extends('layouts.frontend')

@section('title', $vacancy->namalowongan . ' - ' . $vacancy->register->perusahaan->nama)

@section('content')
<!-- Hero / Header Section -->
<div class="bg-primary-theme py-5 position-relative overflow-hidden" style="padding-top: 150px !important; padding-bottom: 80px !important;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 text-white">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white opacity-75">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="#" class="text-white opacity-75">Lowongan</a></li>
                        <li class="breadcrumb-item active text-white fw-bold" aria-current="page">Detail</li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold mb-3 lh-sm">{{ $vacancy->namalowongan }}</h1>
                <div class="d-flex flex-wrap align-items-center gap-4 mt-4">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-50px me-3">
                            @if($vacancy->register->perusahaan->logo)
                            <img src="{{ asset('storage/'.$vacancy->register->perusahaan->logo) }}" class="rounded-circle shadow-sm bg-white p-1" style="width: 50px; height: 50px; object-fit: contain;">
                            @else
                            <span class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="material-icons text-primary-theme">business</i>
                            </span>
                            @endif
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $vacancy->register->perusahaan->nama }}</h5>
                            <span class="opacity-75 fs-7">{{ $vacancy->register->perusahaan->kategori->nama ?? 'Umum' }}</span>
                        </div>
                    </div>
                    <div class="vr opacity-25 d-none d-md-block" style="height: 40px;"></div>
                    <div class="d-flex align-items-center">
                        <i class="material-icons me-2 opacity-75">event</i>
                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#eventModal" class="text-white fw-medium text-decoration-none border-bottom border-white border-opacity-25 hover-opacity-100 transition-all">
                            {{ $vacancy->register->even->namaperiode }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mt-5 mt-lg-0 text-lg-end">
                @auth
                @if(auth()->user()->hasRole('Pelamar'))
                @if($applyStats['has_applied_this'])
                <button class="btn btn-success px-5 py-3 fs-5 rounded-pill shadow-lg disabled">
                    <i class="material-icons align-middle me-1">check_circle</i> Sudah Dilamar
                </button>
                @elseif($applyStats['limit_reached'])
                <button class="btn btn-danger px-5 py-3 fs-5 rounded-pill shadow-lg disabled">
                    <i class="material-icons align-middle me-1">block</i> Batas Lamaran Tercapai
                </button>
                @elseif($applyStats['global_limit_reached'])
                <button class="btn btn-secondary px-5 py-3 fs-5 rounded-pill shadow-lg disabled">
                    <i class="material-icons align-middle me-1">lock</i> Kuota Event Penuh
                </button>
                @else
                <button type="button" data-bs-toggle="modal" data-bs-target="#applyModal" class="btn btn-theme px-5 py-3 fs-5 rounded-pill shadow-lg sparkle-btn">
                    <i class="material-icons align-middle me-1">ads_click</i> Lamar Sekarang
                </button>
                @endif
                @else
                <button class="btn btn-light-theme px-5 py-3 fs-5 rounded-pill shadow-lg disabled">
                    Hanya Untuk Pelamar
                </button>
                @endif
                @else
                <a href="{{ route('login') }}?redirect={{ urlencode(url()->current()) }}" class="btn btn-theme px-5 py-3 fs-5 rounded-pill shadow-lg sparkle-btn">
                    <i class="material-icons align-middle me-1">login</i> Login Untuk Melamar
                </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Event -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-dark text-white p-5 border-0">
                <div class="d-flex align-items-center">
                    <i class="material-icons text-primary-theme me-3 fs-1">campaign</i>
                    <div>
                        <h5 class="modal-title fw-bold" id="eventModalLabel">{{ $vacancy->register->even->namaperiode }}</h5>
                        <ul class="nav nav-pills nav-pills-custom mt-2 gap-2" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active py-2 px-4 rounded-pill fs-8 fw-bold" id="info-tab" data-bs-toggle="pill" href="#tab-info" role="tab">Informasi Event</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 px-4 rounded-pill fs-8 fw-bold" id="vacancies-tab" data-bs-toggle="pill" href="#tab-vacancies" role="tab">Lowongan Tersedia ({{ $eventVacancies->count() }})</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="tab-content">
                    <!-- Tab Informasi -->
                    <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                        <div class="row g-0">
                            <div class="col-md-5 bg-light p-5 text-center border-end">
                                @if($vacancy->register->even->gambar)
                                <img src="{{ asset('storage/'.$vacancy->register->even->gambar) }}" class="img-fluid rounded-4 shadow-sm mb-4" alt="Event Image">
                                @else
                                <div class="p-5 bg-white rounded-circle shadow-sm mb-4 d-inline-block">
                                    <i class="material-icons fs-5x text-muted opacity-25">event</i>
                                </div>
                                @endif
                                <div class="px-3">
                                    <span class="badge bg-primary-theme px-4 py-2 rounded-pill mb-2">EVENT AKTIF</span>
                                    <p class="text-muted fs-8">Klik <a href="https://www.google.com/maps/search/?api=1&query={{ $vacancy->register->even->latitude }},{{ $vacancy->register->even->longitude }}" target="_blank" class="text-primary-theme fw-bold">di sini</a> untuk petunjuk arah</p>
                                </div>
                            </div>
                            <div class="col-md-7 p-5">
                                <h6 class="fw-bold text-dark text-uppercase fs-8 ls-1 mb-3">Visi & Deskripsi</h6>
                                <p class="text-muted fs-7 lh-lg italic">"{{ $vacancy->register->even->visi }}"</p>
                                <p class="text-gray-800 fs-7">{{ $vacancy->register->even->keterangan }}</p>

                                <div class="border-top pt-4">
                                    <h6 class="fw-bold text-dark text-uppercase fs-8 ls-1 mb-3">Agenda & Lokasi</h6>
                                    <div class="d-flex align-items-baseline mb-3">
                                        <i class="material-icons text-danger fs-6 me-2">calendar_today</i>
                                        <span class="text-dark fw-bold fs-7">{{ \Carbon\Carbon::parse($vacancy->register->even->tanggalawal)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($vacancy->register->even->tanggalselesai)->format('d M Y') }}</span>
                                    </div>
                                    <div class="d-flex align-items-baseline">
                                        <i class="material-icons text-primary fs-6 me-2">place</i>
                                        <span class="text-dark fw-bold fs-7">{{ $vacancy->register->even->lokasi }} <span class="fw-normal text-muted">({{ $vacancy->register->even->alamat_lengkap }})</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Daftar Lowongan -->
                    <div class="tab-pane fade" id="tab-vacancies" role="tabpanel">
                        <div class="p-5">
                            <h6 class="fw-bold text-dark text-uppercase fs-8 ls-1 mb-4">Peluang Karir di Event Ini</h6>
                            <div class="d-flex flex-column gap-3">
                                @foreach($eventVacancies as $v)
                                <div class="card card-hover border-light shadow-sm-hover rounded-4 overflow-hidden {{ $v->id == $vacancy->id ? 'border-primary-theme border-1 bg-primary-theme bg-opacity-5' : '' }}">
                                    <div class="p-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-40px me-3">
                                                        @if($v->register->perusahaan->logo)
                                                        <img src="{{ asset('storage/'.$v->register->perusahaan->logo) }}" class="rounded-circle p-1 bg-white shadow-sm" style="width: 40px; height: 40px; object-fit: contain;">
                                                        @else
                                                        <span class="symbol-label bg-light"><i class="material-icons text-muted">business</i></span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1 fw-bold {{ $v->id == $vacancy->id ? 'text-primary-theme' : 'text-dark' }}">{{ $v->namalowongan }}</h6>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="text-muted fs-8 fw-semibold">{{ $v->register->perusahaan->nama }}</span>
                                                            <span class="badge bg-light text-danger fs-9 px-2">Rp {{ number_format($v->gaji_awal, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                @if($v->id == $vacancy->id)
                                                <span class="badge bg-primary-theme text-white rounded-pill px-3 py-1 fs-9">SEDANG DILIHAT</span>
                                                @elseif(in_array($v->id, $applyStats['applied_vacancy_ids']))
                                                <span class="badge bg-success text-white rounded-pill px-3 py-1 fs-9"><i class="material-icons fs-10 align-middle">check</i> SUDAH DILAMAR</span>
                                                @else
                                                <a href="{{ route('vacancy.detail', encrypt($v->id)) }}" class="btn btn-sm btn-outline-danger rounded-pill fw-bold">LIHAT DETAIL</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-4 border-0">
                <button type="button" class="btn btn-outline-dark px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Kembali</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Lamaran -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-body p-5">
                <div class="text-center mb-4">
                    <div class="bg-primary-theme bg-opacity-10 w-80px h-80px rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4">
                        <i class="material-icons text-primary-theme fs-1">send</i>
                    </div>
                    <h4 class="fw-bold text-dark">Lamar Pekerjaan Ini?</h4>
                    <p class="text-muted small">Biodata Anda akan dikirimkan secara klinis ke <strong>{{ $vacancy->register->perusahaan->nama }}</strong> untuk posisi <strong>{{ $vacancy->namalowongan }}</strong>.</p>
                </div>

                <div class="bg-light rounded-4 p-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="material-icons text-info me-2 fs-5">info</i>
                        <span class="fw-bold text-dark fs-7">Statistik Lamaran Anda</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fs-8">Lamaran ke-</span>
                        <span class="fw-bold text-dark fs-8">{{ $applyStats['count_in_event'] + 1 }} di event ini</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fs-8">Batas Maksimal</span>
                        <span class="fw-bold text-dark fs-8">{{ $vacancy->register->even->maksimum_apply }} Lamaran</span>
                    </div>
                </div>

                <form id="formApplyJob">
                    @csrf
                    <input type="hidden" name="idlowongan" value="{{ $vacancy->id }}">

                    {{-- Seleksi Tanggal Kedatangan --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark fs-8 text-uppercase ls-1">Pilih Tanggal Kedatangan</label>
                        <select name="tanggal_datang" class="form-select form-select-lg rounded-3 fs-7" required>
                            <option value="" disabled selected>-- Pilih Tanggal --</option>
                            @php
                            $startDate = \Carbon\Carbon::parse($vacancy->register->even->tanggalawal);
                            $endDate = \Carbon\Carbon::parse($vacancy->register->even->tanggalselesai);
                            while($startDate <= $endDate) {
                                echo '<option value="' .$startDate->toDateString().'">'.$startDate->translatedFormat('d F Y').'</option>';
                                $startDate->addDay();
                                }
                                @endphp
                        </select>
                        <div class="form-text fs-9 text-muted mt-2">Pilih tanggal rencana kehadiran Anda di lokasi event.</div>
                    </div>

                    @if($vacancy->register->even->status_sesi == 1)
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark fs-8 text-uppercase ls-1">Pilih Sesi Kehadiran</label>
                        <select name="idsesi" class="form-select form-select-lg rounded-3 fs-7" required>
                            <option value="" disabled selected>-- Pilih Sesi --</option>
                            @foreach($vacancy->register->even->sesis as $sesi)
                            <option value="{{ $sesi->id }}">{{ $sesi->nama_sesi }} ({{ $sesi->jam_mulai }} - {{ $sesi->jam_selesai }})</option>
                            @endforeach
                        </select>
                        <div class="form-text fs-9 text-muted mt-2">Pilih sesi waktu yang tersedia.</div>
                    </div>
                    @else
                    <input type="hidden" name="idsesi" value="0">
                    @endif

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-theme py-3 rounded-pill fw-bold shadow-sm" id="btnSubmitApply">
                            Kirim Lamaran Sekarang
                        </button>
                        <button type="button" class="btn btn-light py-3 rounded-pill fw-bold text-muted border-0" data-bs-dismiss="modal">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Section -->
<div class="container py-5 mt-n5 position-relative z-index-2">
    <div class="row g-5">
        <!-- Job Description & Requirements -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                <div class="card-body p-5">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-light">
                        <i class="material-icons text-primary-theme me-2 fs-1">description</i>
                        <h3 class="fw-bold text-dark mb-0">Informasi Pekerjaan</h3>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 bg-light border-0 h-100">
                                <span class="text-muted d-block mb-1 fs-8 fw-bold text-uppercase ls-1">Penempatan</span>
                                <div class="d-flex align-items-center">
                                    <i class="material-icons text-danger me-2">place</i>
                                    <span class="text-dark fw-bold fs-6">{{ $vacancy->kategorilokasi }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 bg-light border-0 h-100">
                                <span class="text-muted d-block mb-1 fs-8 fw-bold text-uppercase ls-1">Estimasi Gaji</span>
                                <div class="d-flex align-items-center">
                                    <i class="material-icons text-success me-2">payments</i>
                                    <span class="text-dark fw-bold fs-6">Rp {{ number_format($vacancy->gaji_awal, 0, ',', '.') }} - {{ number_format($vacancy->gaji_akhir, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 bg-light border-0 h-100">
                                <span class="text-muted d-block mb-1 fs-8 fw-bold text-uppercase ls-1">Kategori Peran</span>
                                <div class="d-flex align-items-center">
                                    <i class="material-icons text-primary me-2">category</i>
                                    <span class="text-dark fw-bold fs-6">{{ $vacancy->kategori->nama ?? 'Umum' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 bg-light border-0 h-100">
                                <span class="text-muted d-block mb-1 fs-8 fw-bold text-uppercase ls-1">Kuota Tersedia</span>
                                <div class="d-flex align-items-center">
                                    <i class="material-icons text-warning me-2">group</i>
                                    <span class="text-dark fw-bold fs-6">{{ $vacancy->kuota }} Posisi</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== PERSYARATAN PELAMAR — compact, 1 icon section only ===== --}}
                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom border-light">
                        <i class="material-icons text-primary-theme me-2 fs-1">fact_check</i>
                        <h3 class="fw-bold text-dark mb-0">Persyaratan Pelamar</h3>
                    </div>

                    @php
                    $pendidikanList = [
                    1 => 'SD', 2 => 'SMP', 3 => 'SMA / SMK', 4 => 'D1',
                    5 => 'D2', 6 => 'D3', 7 => 'D4 / S1', 8 => 'S2', 9 => 'S3',
                    ];
                    $exp = $vacancy->minimal_pengalaman_bulan ?? 0;
                    @endphp

                    <div class="d-flex flex-wrap rounded-4 bg-light p-4 mb-4">
                        <div class="flex-fill px-3 py-1 border-end border-secondary border-opacity-10">
                            <span class="text-muted d-block fs-8 fw-bold text-uppercase ls-1">Gender</span>
                            <span class="text-dark fw-bold fs-6">{{ $vacancy->preferensi_gender ?? 'Semua' }}</span>
                        </div>
                        <div class="flex-fill px-3 py-1 border-end border-secondary border-opacity-10">
                            <span class="text-muted d-block fs-8 fw-bold text-uppercase ls-1">Pendidikan</span>
                            <span class="text-dark fw-bold fs-6">{{ $pendidikanList[$vacancy->minimal_pendidikan] ?? 'Bebas' }}</span>
                        </div>
                        <div class="flex-fill px-3 py-1 border-end border-secondary border-opacity-10">
                            <span class="text-muted d-block fs-8 fw-bold text-uppercase ls-1">Usia</span>
                            <span class="text-dark fw-bold fs-6">
                                @if($vacancy->usia_min || $vacancy->usia_max)
                                {{ $vacancy->usia_min ?? '-' }}-{{ $vacancy->usia_max ?? '-' }} thn
                                @else
                                Bebas
                                @endif
                            </span>
                        </div>
                        <div class="flex-fill px-3 py-1">
                            <span class="text-muted d-block fs-8 fw-bold text-uppercase ls-1">Pengalaman</span>
                            <span class="text-dark fw-bold fs-6">
                                @if($exp == 0)
                                Fresh Graduate
                                @else
                                {{ floor($exp / 12) }} thn {{ $exp % 12 }} bln
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-5">
                            <span class="text-muted d-block mb-2 fs-8 fw-bold text-uppercase ls-1">Jurusan Diterima</span>
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($vacancy->jurusans as $j)
                                <span class="badge fs-8 py-2 px-3" style="background-color:#e8f0fe; color:#1967d2; font-weight:600; border-radius:6px;">
                                    {{ $j->jurusan->namajurusan ?? '-' }}
                                </span>
                                @empty
                                <span class="text-muted fs-7">Semua jurusan</span>
                                @endforelse
                            </div>
                        </div>
                        <div class="col-md-7">
                            <span class="text-muted d-block mb-2 fs-8 fw-bold text-uppercase ls-1">Skill Dibutuhkan</span>
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($vacancy->skills as $s)
                                <span class="badge fs-8 py-2 px-3" style="background-color:#e6f4ea; color:#188038; font-weight:600; border-radius:6px;">
                                    {{ $s->skill->namaskill ?? '-' }}
                                </span>
                                @empty
                                <span class="text-muted fs-7">Tidak ada syarat khusus</span>
                                @endforelse
                            </div>
                        </div>
                       
                    </div>

                    <div class="job-rich-content">
                        <div class="fs-6 text-gray-800 lh-lg">
                            {!! $vacancy->deskripsi !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Mini Bio -->
            <div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-5 mb-5 overflow-hidden position-relative">
                <div class="position-absolute top-0 end-0 opacity-10">
                    <i class="material-icons fs-10x">business</i>
                </div>
                <div class="position-relative z-index-2">
                    <h4 class="fw-bold mb-3">Tentang {{ $vacancy->register->perusahaan->nama }}</h4>
                    <p class="opacity-75 mb-4 line-clamp-3">{{ $vacancy->register->perusahaan->gambaranumum ?? 'Perusahaan ini belum menyertakan gambaran umum.' }}</p>
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#companyModal" class="text-white fw-bold text-decoration-none d-flex align-items-center">
                        Pelajari Profil Perusahaan <i class="material-icons ms-2 fs-6">arrow_forward</i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar / Recruitment Context -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">


                        <div class="mb-5">
                            <label class="text-muted fs-8 fw-bold text-uppercase ls-1 mb-2 d-block">Terintegrasi Dengan Event</label>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#eventModal" class="text-decoration-none transition-all hover-translate-y-n2 d-block">
                                <div class="p-4 border rounded-4 d-flex align-items-center bg-white shadow-sm-hover transition-all">
                                    <div class="symbol symbol-40px me-3">
                                        <span class="symbol-label bg-primary-theme bg-opacity-10">
                                            <i class="material-icons text-primary-theme">campaign</i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold text-dark">{{ $vacancy->register->even->namaperiode }}</h6>
                                        <span class="text-muted fs-8 d-block">
                                            {{ \Carbon\Carbon::parse($vacancy->register->even->tanggalawal)->format('d M') }} - {{ \Carbon\Carbon::parse($vacancy->register->even->tanggalselesai)->format('d M Y') }}
                                        </span>
                                    </div>
                                    <i class="material-icons text-muted opacity-50 fs-5">chevron_right</i>
                                </div>
                            </a>
                        </div>

                        <div class="d-grid gap-3">
                            @auth
                            @if(auth()->user()->hasRole('Pelamar'))
                            @if($applyStats['has_applied_this'])
                            <button class="btn btn-success py-3 rounded-pill fw-bold disabled">
                                <i class="material-icons align-middle me-1">check_circle</i> Sudah Dilamar
                            </button>
                            @elseif($applyStats['limit_reached'])
                            <button class="btn btn-danger py-3 rounded-pill fw-bold disabled">
                                <i class="material-icons align-middle me-1">block</i> Batas Lamaran Tercapai
                            </button>
                            @elseif($applyStats['global_limit_reached'])
                            <button class="btn btn-secondary py-3 rounded-pill fw-bold disabled">
                                <i class="material-icons align-middle me-1">lock</i> Kuota Event Penuh
                            </button>
                            @else
                            <button type="button" data-bs-toggle="modal" data-bs-target="#applyModal" class="btn btn-theme py-3 rounded-pill fw-bold">
                                Kirim Lamaran Sekarang
                            </button>
                            @endif
                            @else
                            <button class="btn btn-light-theme py-3 rounded-pill fw-bold disabled">
                                Hanya Untuk Pelamar
                            </button>
                            @endif
                            @else
                            <a href="{{ route('login') }}?redirect={{ urlencode(url()->current()) }}" class="btn btn-theme py-3 rounded-pill fw-bold">Login Untuk Melamar</a>
                            @endauth

                            <button id="btn_wishlist" class="btn btn-outline-dark py-3 rounded-pill fw-bold d-flex align-items-center justify-content-center w-100 mb-3 {{ $applyStats['is_wishlisted'] ? 'active' : '' }}" data-id="{{ $vacancy->id }}">
                                <i class="material-icons wishlist-icon me-2 fs-5 text-danger">{{ $applyStats['is_wishlisted'] ? 'favorite' : 'favorite_border' }}</i>
                                <span class="wishlist-text">{{ $applyStats['is_wishlisted'] ? 'Hapus Dari Wishlist' : 'Simpan Ke Wishlist' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-primary-theme bg-opacity-5 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="material-icons text-primary-theme me-2">help_outline</i>
                        <h6 class="fw-bold mb-0">Butuh Bantuan?</h6>
                    </div>
                    <p class="text-muted fs-7 mb-0">Hubungi tim support kami jika Anda mengalami kendala saat melamar pekerjaan ini.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .mt-n5 {
        margin-top: -3rem !important;
    }

    .z-index-2 {
        z-index: 2;
    }

    .ls-1 {
        letter-spacing: 0.5px;
    }

    .bg-light-success {
        background: rgba(25, 135, 84, 0.05);
    }

    .job-rich-content ul,
    .job-rich-content ol {
        padding-left: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .job-rich-content p {
        margin-bottom: 1.25rem;
    }

    .sparkle-btn {
        transition: all 0.3s;
        box-shadow: 0 10px 30px -10px rgba(127, 29, 29, 0.5) !important;
    }

    .sparkle-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 40px -12px rgba(127, 29, 29, 0.6) !important;
    }

    .hover-translate-y-n2:hover {
        transform: translateY(-5px);
    }

    .shadow-sm-hover:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
        background-color: #fff !important;
        border-color: var(--primary-theme) !important;
    }
</style>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.brighttheme.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.buttons.js"></script>
<script>
    $(document).ready(function() {
        $('#formApplyJob').on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();
            let btn = $('#btnSubmitApply');
            let originalText = btn.html();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');

            $.ajax({
                url: "{{ route('pelamar.apply-job') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            showConfirmButton: true,
                            confirmButtonText: 'Ke Dashboard Saya',
                            customClass: {
                                confirmButton: 'btn btn-theme px-5 py-2 rounded-pill fw-bold'
                            }
                        }).then((result) => {
                            window.location.href = "{{ route('pelamar.dashboard') }}";
                        });
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';

                    new PNotify({
                        title: 'Gagal Melamar',
                        text: msg,
                        type: 'error',
                        styling: 'brighttheme',
                        delay: 4000
                    });
                }
            });
        });

        // Toggle Wishlist Logic
        $('#btn_wishlist').on('click', function(e) {
            e.preventDefault();

            @if(!auth() -> check())
            window.location.href = "{{ route('login') }}?redirect={{ urlencode(url()->current()) }}";
            return;
            @endif

            let btn = $(this);
            let vacancyId = btn.data('id');
            let icon = btn.find('.wishlist-icon');
            let text = btn.find('.wishlist-text');

            btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('wishlist.toggle') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    idlowongan: vacancyId
                },
                success: function(response) {
                    btn.prop('disabled', false);
                    if (response.status === 'success') {
                        if (response.action === 'added') {
                            icon.text('favorite');
                            text.text('Hapus Dari Wishlist');
                            btn.addClass('active');
                        } else {
                            icon.text('favorite_border');
                            text.text('Simpan Ke Wishlist');
                            btn.removeClass('active');
                        }

                        new PNotify({
                            title: 'Wishlist',
                            text: response.message,
                            type: 'success',
                            styling: 'brighttheme',
                            delay: 3000
                        });
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';

                    new PNotify({
                        title: 'Akses Ditolak',
                        text: msg,
                        type: 'error',
                        styling: 'brighttheme',
                        delay: 4000
                    });
                }
            });
        });
    });
</script>
@endpush
<!-- Modal Profil Perusahaan -->
<div class="modal fade" id="companyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5 pt-0">
                <div class="text-center mb-5">
                    <div class="symbol symbol-100px mb-4">
                        @if($vacancy->register->perusahaan->logo)
                        <img src="{{ asset('storage/'.$vacancy->register->perusahaan->logo) }}" class="rounded-circle shadow-sm border p-2 bg-white" style="width: 100px; height: 100px; object-fit: contain;">
                        @else
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 100px; height: 100px;">
                            <i class="material-icons text-primary-theme fs-4x">business</i>
                        </div>
                        @endif
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $vacancy->register->perusahaan->nama }}</h3>
                    <span class="badge bg-primary-theme bg-opacity-10 text-primary-theme px-3 py-2 rounded-pill fw-bold">
                        {{ $vacancy->register->perusahaan->kategori->nama ?? 'Sektor Umum' }}
                    </span>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 rounded-4 bg-light">
                            <i class="material-icons text-primary-theme me-3">link</i>
                            <div>
                                <span class="d-block text-muted fs-9 fw-bold text-uppercase">Website</span>
                                @if($vacancy->register->perusahaan->website)
                                <a href="{{ $vacancy->register->perusahaan->website }}" target="_blank" class="text-dark fw-bold fs-7 text-decoration-none hover-primary">{{ $vacancy->register->perusahaan->website }}</a>
                                @else
                                <span class="text-dark fw-bold fs-7">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 rounded-4 bg-light">
                            <i class="material-icons text-primary-theme me-3">groups</i>
                            <div>
                                <span class="d-block text-muted fs-9 fw-bold text-uppercase">Ukuran Perusahaan</span>
                                <span class="text-dark fw-bold fs-7">{{ $vacancy->register->perusahaan->jumlah_karyawan ?? '1 - 50' }} Karyawan</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 rounded-4 bg-light">
                            <i class="material-icons text-primary-theme me-3">event</i>
                            <div>
                                <span class="d-block text-muted fs-9 fw-bold text-uppercase">Berdiri Sejak</span>
                                <span class="text-dark fw-bold fs-7">{{ $vacancy->register->perusahaan->tahunberdiri ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 rounded-4 bg-light">
                            <i class="material-icons text-primary-theme me-3">place</i>
                            <div>
                                <span class="d-block text-muted fs-9 fw-bold text-uppercase">Lokasi Kantor</span>
                                <span class="text-dark fw-bold fs-7">{{ Str::limit($vacancy->register->perusahaan->alamatlengkap, 20) ?? 'Indonesia' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="company-bio border-top pt-5">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <i class="material-icons text-primary-theme me-2">info</i> Profil Singkat
                    </h5>
                    <div class="text-gray-700 lh-lg fs-7">
                        {!! nl2br(e($vacancy->register->perusahaan->gambaranumum ?? 'Informasi profil perusahaan belum tersedia.')) !!}
                    </div>
                </div>

                @if($vacancy->register->perusahaan->alamatlengkap)
                <div class="company-address mt-5 border-top pt-5">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <i class="material-icons text-danger me-2">map</i> Alamat Lengkap
                    </h5>
                    <p class="text-gray-700 fs-7 mb-0">
                        {{ $vacancy->register->perusahaan->alamatlengkap }}
                    </p>
                </div>
                @endif
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-outline-dark px-5 rounded-pill fw-bold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-primary:hover {
        color: var(--primary-theme) !important;
        text-decoration: underline !important;
    }
</style>

@endsection
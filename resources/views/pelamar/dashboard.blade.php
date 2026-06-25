@extends('layouts.frontend')

@section('title', 'Dashboard Pelamar - FindTalen')

@section('content')
<div class="pb-10 bg-white" style="min-height: 100vh;">

    {{-- ===== HEADER BANNER ===== --}}
    <div class="bg-primary-theme">
        <div class="container-fluid px-0">
            <div class="pt-8 pb-16 px-4 px-md-15 mb-n10">
                <div class="container px-0 px-md-3">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4 text-center text-md-start">
                        <div class="d-flex flex-column flex-md-row align-items-center">
                            <div class="border border-4 border-white border-opacity-30 shadow-lg rounded-circle mb-4 mb-md-0 me-md-6 overflow-hidden mx-auto" style="width:110px;height:110px;min-width:110px;background:white;">
                                @if($pelamar->foto && $pelamar->foto != 'no-image')
                                <img src="{{ asset('storage/'.$pelamar->foto) }}" alt="user" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                <div class="text-primary-theme fw-bold fs-1 d-flex align-items-center justify-content-center w-100 h-100">{{ substr($pelamar->namalengkap, 0, 1) }}</div>
                                @endif
                            </div>
                            <div class="px-md-2">
                                <h1 class="fw-extrabold text-white mb-2 fs-1 ls-n1">Selamat Datang, {{ explode(' ', $pelamar->namalengkap)[0] }}! 👋</h1>
                                <p class="text-white text-opacity-85 mb-0 fs-7 mx-auto mx-md-0 lh-base" style="max-width:500px;">Optimalkan pencarian kerjamu dan pantau status lamaran secara instan.</p>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-sm-row gap-3 w-100 w-md-auto px-2 px-md-0">
                            <button type="button" class="btn btn-white w-100 w-sm-auto px-6 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center h-50px" onclick="startScanner()">
                                <i class="material-icons text-success me-2">qr_code_scanner</i> Scan Absensi
                            </button>
                            <a href="{{ route('frontend.events') }}" class="btn btn-warning w-100 w-sm-auto px-6 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center h-50px text-dark">
                                <i class="material-icons me-2">explore</i> Cari Lowongan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-n6 px-3 px-md-3">

        {{-- ===== METRICS GRID ===== --}}
        <div class="row g-3 mb-5 mt-2">
            <div class="col-lg-4 col-sm-6">
                <div class="stat-card h-100">
                    <div class="stat-icon" style="background:rgba(13,110,253,0.08);color:#751e18;">
                        <i class="material-icons">history</i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $totalEventsFollowed }}</h3>
                        <span class="fs-8 text-muted fw-medium">Event Diikuti</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="stat-card h-100">
                    <div class="stat-icon bg-light-danger">
                        <i class="material-icons">upcoming</i>
                    </div>
                    <div>
                        <div class="d-flex align-items-baseline gap-2">
                            <h3 class="fw-bold mb-0">{{ $totalUpcomingEvents }}</h3>
                            <span class="badge bg-danger bg-opacity-10 text-danger fs-10 px-2">{{ $totalUpcomingApplies }} Lamaran</span>
                        </div>
                        <span class="fs-8 text-muted fw-medium">Event Akan Datang</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-12">
                <div class="stat-card h-100">
                    <div class="stat-icon" style="background:rgba(25,135,84,0.08);color:#198754;">
                        <i class="material-icons">verified_user</i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h3 class="fw-bold mb-0">{{ $profileCompletion }}%</h3>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-success" style="width:{{ $profileCompletion }}%"></div>
                        </div>
                        <span class="fs-9 text-muted fw-medium mt-1 d-block">Kelengkapan Profil</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== MANAJEMEN KARTU EVENT ===== --}}
        @if($upcomingGroups->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                    <i class="material-icons text-danger me-2">badge</i> Manajemen Kartu Event
                </h5>
                <p class="text-muted fs-9 mb-0">Cetak kartu peserta untuk dibawa ke lokasi bursa kerja.</p>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @foreach($upcomingGroups as $ideven => $lams)
                    @php $even = $lams->first()->even; @endphp
                    <div class="col-md-6 col-xl-4">
                        <div class="p-4 rounded-4 border bg-white shadow-sm h-100">
                            <div class="badge bg-danger bg-opacity-10 text-danger mb-2 fs-10 fw-bold px-2 py-1 rounded-pill">{{ $lams->count() }} Lamaran Terdaftar</div>
                            <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $even->namaperiode }}</h6>
                            <p class="text-muted fs-9 mb-3"><i class="material-icons fs-10 align-middle">calendar_today</i> {{ \Carbon\Carbon::parse($even->tanggalawal)->format('d M Y') }}</p>
                            <a href="{{ route('pelamar.print-card', encrypt($even->id)) }}" target="_blank" class="btn btn-sm btn-danger w-100 rounded-pill py-2 fw-bold">
                                <i class="material-icons fs-6 align-middle me-1">print</i> Cetak Kartu Peserta
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ===== MAIN GRID ===== --}}
        {{-- =========================== ROW 1: Lamaran + Wishlist =========================== --}}
        <div class="row g-4 align-items-stretch mb-4">

            {{-- Lamaran Terakhir --}}
            <div class="col-xl-8 col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0">Lamaran Terakhir</h5>
                            @if($recentApplications->count() > 3)
                            <button id="btn-toggle-lamaran" class="btn btn-sm btn-light fs-8 fw-bold px-3 rounded-pill text-primary-theme"
                                onclick="toggleSection('lamaran')">
                                Lihat Semua ({{ $recentApplications->count() }})
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        {{-- Mobile cards --}}
                        <div class="d-block d-md-none vstack gap-3">
                            @forelse($recentApplications as $i => $app)
                            @php
                            $isHadir = $app->kehadirans->isNotEmpty();
                            $evenDate = \Carbon\Carbon::parse($app->even->tanggalawal);
                            $isToday = now()->isSameDay($evenDate);
                            $diff = round(now()->diffInDays($evenDate, false));
                            $statusText = $isHadir ? 'Hadir' : match($app->statusditerima) { '0'=>'Melamar','2'=>'Wawancara','3'=>'Diterima',default=>'Pending' };
                            $statusClass = $isHadir ? 'bg-success' : match($app->statusditerima) { '0'=>'bg-secondary','2'=>'bg-primary','3'=>'bg-success',default=>'bg-secondary' };
                            @endphp
                            <a href="{{ route('vacancy.detail', encrypt($app->lowongan->id)) }}"
                                class="lamaran-card text-decoration-none text-dark p-3 rounded-4 border d-block{{ $i >= 3 ? ' d-none lamaran-extra' : '' }}">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bg-light rounded-3 p-1 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width:42px;height:42px;">
                                        @if($app->lowongan->register->perusahaan->logo)
                                        <img src="{{ asset('storage/'.$app->lowongan->register->perusahaan->logo) }}" style="width:28px;height:28px;object-fit:contain;">
                                        @else
                                        <i class="material-icons text-muted fs-5">business</i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-bold text-dark fs-7 text-truncate">{{ $app->lowongan->namalowongan }}</div>
                                        <div class="fs-8 text-muted text-truncate">{{ $app->lowongan->register->perusahaan->nama }}</div>
                                    </div>
                                    <span class="badge {{ $statusClass }} bg-opacity-15 text-{{ str_replace('bg-', '', $statusClass) }} rounded-pill px-2 py-1 fs-10 flex-shrink-0">{{ $statusText }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-9 text-muted"><i class="material-icons fs-10 align-middle">event</i> {{ \Carbon\Carbon::parse($app->even->tanggalawal)->format('d M Y') }}</span>
                                    @if($diff > 0)
                                    <span class="badge bg-primary bg-opacity-10 text-primary fs-10 rounded-pill px-2">{{ $diff }} hari lagi</span>
                                    @elseif($diff == 0)
                                    <span class="badge bg-danger rounded-pill px-2 fs-10">HARI INI</span>
                                    @else
                                    <span class="text-muted fs-10">Selesai</span>
                                    @endif
                                </div>
                                @if(!$isHadir && $isToday)
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-light-success fw-bold fs-10 py-1 px-3 rounded-pill border border-success border-opacity-20"
                                        onclick="event.preventDefault(); startScanner()">
                                        <i class="material-icons fs-9 align-middle">qr_code_scanner</i> Absen Sekarang
                                    </button>
                                </div>
                                @endif
                            </a>
                            @empty
                            <div class="text-center py-5">
                                <i class="material-icons fs-2 text-muted opacity-50">inbox</i>
                                <p class="text-muted small fw-medium mt-2">Belum ada lamaran yang terkirim.</p>
                            </div>
                            @endforelse
                        </div>

                        {{-- Desktop table --}}
                        <div class="d-none d-md-block table-responsive">
                            <table class="table align-middle border-0">
                                <thead class="bg-light fs-9 text-uppercase ls-1 text-muted">
                                    <tr>
                                        <th class="ps-3 border-0 py-3 rounded-start-3">Posisi &amp; Perusahaan</th>
                                        <th class="border-0">Event Rekrutmen</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 text-center rounded-end-3">Hadir Dalam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentApplications as $i => $app)
                                    @php
                                    $isHadir = $app->kehadirans->isNotEmpty();
                                    $evenDate = \Carbon\Carbon::parse($app->even->tanggalawal);
                                    $isToday = now()->isSameDay($evenDate);
                                    $diff = round(now()->diffInDays($evenDate, false));
                                    $statusText = $isHadir ? 'Hadir' : match($app->statusditerima) { '0'=>'Melamar','2'=>'Wawancara','3'=>'Diterima',default=>'Pending' };
                                    $statusClass = $isHadir ? 'bg-success' : match($app->statusditerima) { '0'=>'bg-secondary','2'=>'bg-primary','3'=>'bg-success',default=>'bg-secondary' };
                                    @endphp
                                    <tr class="lamaran-row{{ $i >= 3 ? ' d-none lamaran-extra' : '' }}"
                                        onclick="window.location='{{ route('vacancy.detail', encrypt($app->lowongan->id)) }}'"
                                        style="cursor:pointer;">
                                        <td class="ps-3 border-0 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-3 me-3 p-1 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width:40px;height:40px;">
                                                    @if($app->lowongan->register->perusahaan->logo)
                                                    <img src="{{ asset('storage/'.$app->lowongan->register->perusahaan->logo) }}" style="width:28px;height:28px;object-fit:contain;">
                                                    @else
                                                    <i class="material-icons text-muted fs-5">business</i>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="fw-bold text-dark fs-7 lh-1 mb-1 text-truncate" style="max-width:180px;">{{ $app->lowongan->namalowongan }}</div>
                                                    <div class="fs-8 text-muted text-truncate" style="max-width:180px;">{{ $app->lowongan->register->perusahaan->nama }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="border-0">
                                            <div class="fw-semibold text-dark fs-8 mb-1">{{ $app->even->namaperiode }}</div>
                                            <div class="d-flex flex-wrap align-items-center gap-1">
                                                <span class="fs-9 text-muted">{{ \Carbon\Carbon::parse($app->even->tanggalawal)->format('d M Y') }}</span>
                                                @if($app->sesi)
                                                <span class="badge bg-light text-primary-theme fs-10 border border-primary-theme border-opacity-10 rounded-pill px-2">
                                                    {{ $app->sesi->nama_sesi }}
                                                </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="border-0">
                                            <div class="d-flex flex-column align-items-start gap-1">
                                                <span class="badge {{ $statusClass }} bg-opacity-10 text-{{ str_replace('bg-', '', $statusClass) }} border border-{{ str_replace('bg-', '', $statusClass) }} border-opacity-25 rounded-pill px-3 py-1 fs-9">{{ $statusText }}</span>
                                                @if(!$isHadir && $isToday)
                                                <button type="button"
                                                    class="btn btn-sm btn-light-success fw-bold fs-10 py-1 px-2 rounded-pill border border-success border-opacity-20"
                                                    onclick="event.stopPropagation(); startScanner()">
                                                    <i class="material-icons fs-9 align-middle">qr_code_scanner</i> Absen
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="border-0 text-center">
                                            @if($diff > 0)
                                            <span class="fw-extrabold text-primary-theme fs-6 d-block lh-1">{{ $diff }}</span>
                                            <span class="text-muted fs-10 text-uppercase">Hari Lagi</span>
                                            @elseif($diff == 0)
                                            <span class="badge bg-danger rounded-pill px-3 py-1 fs-9">HARI INI</span>
                                            @else
                                            <span class="text-muted fs-9 opacity-50">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px;height:60px;">
                                                <i class="material-icons fs-2 text-muted opacity-50">inbox</i>
                                            </div>
                                            <p class="text-muted small fw-medium">Belum ada lamaran yang terkirim.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Wishlist / Lowongan Tersimpan --}}
            <div class="col-xl-4 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                                <i class="material-icons text-danger me-2">favorite</i> Lowongan Tersimpan
                            </h6>
                            @if($wishlistedJobs->count() > 3)
                            <button id="btn-toggle-wishlist" class="btn btn-sm btn-light fs-8 fw-bold px-3 rounded-pill text-primary-theme"
                                onclick="toggleSection('wishlist')">
                                Lihat Semua ({{ $wishlistedJobs->count() }})
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="vstack gap-2">
                            @forelse($wishlistedJobs as $i => $wish)
                            @php $loker = $wish->lowongan; @endphp
                            <a href="{{ route('vacancy.detail', encrypt($loker->id)) }}"
                                class="wishlist-card text-decoration-none text-dark p-3 rounded-4 border d-block wishlist-item{{ $i >= 3 ? ' d-none wishlist-extra' : '' }}">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="bg-light rounded-3 p-2 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width:44px;height:44px;">
                                        @if($loker->register->perusahaan->logo)
                                        <img src="{{ asset('storage/'.$loker->register->perusahaan->logo) }}" style="max-width:28px;max-height:28px;object-fit:contain;">
                                        @else
                                        <i class="material-icons text-muted fs-6">business</i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="fw-bold text-dark mb-1 lh-sm" style="font-size:13px;word-break:break-word;white-space:normal;">{{ $loker->namalowongan }}</h6>
                                        <div class="fs-9 text-muted text-truncate mb-2">{{ $loker->register->perusahaan->nama }}</div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge bg-primary bg-opacity-10 text-primary fs-10 px-2 py-1 rounded-pill">{{ $loker->kategori->nama ?? 'Sektor Umum' }}</span>
                                            <span class="text-success fw-semibold d-flex align-items-center" style="font-size:11px;">
                                                <i class="material-icons me-1" style="font-size:11px;">payments</i>
                                                {{ number_format($loker->gaji_awal / 1000000, 0) }}–{{ number_format($loker->gaji_akhir / 1000000, 0) }}jt
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @empty
                            <div class="text-center py-4 bg-light rounded-4">
                                <i class="material-icons text-muted opacity-25 mb-2" style="font-size:36px;">favorite_border</i>
                                <p class="text-muted small mb-0">Belum ada lowongan yang disimpan.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /ROW 1 --}}


        {{-- =========================== ROW 2: Rekomendasi Lowongan =========================== --}}
        <div class="row g-4 mb-2">
            <div class="col-12">
                @include('pelamar.rekomendasi-section')
            </div>
        </div>{{-- /ROW 2 --}}


        {{-- =========================== ROW 3: Event Readiness + Sidebar kanan =========================== --}}
        @if($nextEvent || $profileCompletion < 100)
            <div class="row g-4 align-items-stretch mb-4">

            {{-- Event Readiness --}}
            @if($nextEvent)
            <div class="col-xl-8 col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 bg-primary-theme text-white overflow-hidden h-100">
                    <div class="card-body p-4 p-xl-5 position-relative">

                        {{-- Header dipindah ke LUAR row --}}
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-white bg-opacity-20 p-3 rounded-circle me-3">
                                <i class="material-icons text-warning fs-3">campaign</i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0">Siapkan Diri Anda!</h4>
                                <p class="opacity-75 mb-0 small text-uppercase ls-1">Event: <strong>{{ $nextEvent->namaperiode }}</strong></p>
                            </div>
                        </div>

                        {{-- Row sekarang hanya berisi cards + kotak H --}}
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="vstack gap-3">
                                    <div class="d-flex align-items-center gap-3 bg-white bg-opacity-10 p-3 rounded-3 border border-white border-opacity-10">
                                        <i class="material-icons text-warning">checkroom</i>
                                        <div>
                                            <div class="fw-bold fs-7">Berpakaian Rapih &amp; Formal</div>
                                            <div class="fs-9 opacity-75">Jas, kemeja, dan sepatu pantofel</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 bg-white bg-opacity-10 p-3 rounded-3 border border-white border-opacity-10">
                                        <i class="material-icons text-warning">description</i>
                                        <div>
                                            <div class="fw-bold fs-7">Bawa Kelengkapan Dokumen</div>
                                            <div class="fs-9 opacity-75">CV fisik, KTP, dan sertifikat pendukung</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mt-4 mt-lg-0">
                                @php $diffDays = round(now()->diffInDays(\Carbon\Carbon::parse($nextEvent->tanggalawal), false)); @endphp
                                <div class="text-center p-4 rounded-4" style="background:rgba(255,255,255,0.05);border:2px dashed rgba(255,255,255,0.2);">
                                    <h1 class="fw-extrabold mb-0" style="font-size:3.5rem;">{{ $diffDays > 0 ? $diffDays : 'H' }}</h1>
                                    <p class="small text-uppercase ls-2 mb-0 opacity-75">{{ $diffDays > 0 ? 'Hari Lagi' : 'HARI INI' }}</p>
                                </div>
                            </div>
                        </div>

                        <i class="material-icons position-absolute top-0 end-0 m-4 opacity-05 fs-1hx">event_available</i>
                    </div>
                </div>
            </div>
            @endif

            {{-- Kanan: Lengkapi CV + Job Fair Promo --}}
            <div class="{{ $nextEvent ? 'col-xl-4 col-lg-5' : 'col-12' }}">
                <div class="d-flex flex-column gap-4 h-100">

                    @if($profileCompletion < 100)
                        <div class="card border-0 shadow-sm rounded-4 bg-primary-theme text-white overflow-hidden flex-shrink-0">
                        <div class="card-body p-4 d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-20 p-3 rounded-4 me-3 d-none d-sm-flex align-items-center justify-content-center">
                                    <i class="material-icons text-warning fs-2">tips_and_updates</i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Tingkatkan Peluang Kerjamu!</h5>
                                    <p class="opacity-80 mb-0 small">Lengkapi pendidikan &amp; pengalaman agar dilirik HRD.</p>
                                </div>
                            </div>
                            <a href="{{ route('pelamar.complete-data') }}" class="btn btn-warning px-4 py-2 fw-bold rounded-pill shadow-sm flex-shrink-0 w-100 w-sm-auto text-center">
                                Lengkapi CV
                            </a>
                        </div>
                </div>
                @endif

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden flex-grow-1">
                    <div class="bg-primary-theme p-4 text-white text-center h-100 d-flex flex-column align-items-center justify-content-center">
                        <h2 class="fw-bold mb-2">Job Fair 2024</h2>
                        <p class="small opacity-75 mb-3">Ikuti event rekrutmen massal dengan ribuan peluang karir.</p>
                        <a href="{{ route('frontend.events') }}" class="btn btn-white btn-sm px-4 fw-bold rounded-pill text-primary-theme">Eksplor Event</a>
                    </div>
                </div>

            </div>
    </div>

</div>{{-- /ROW 3 --}}
@endif
</div>
</div>
</div>

{{-- QR Scanner Modal --}}
<div class="modal fade" id="qrScannerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0">Scan QR Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="stopScanner()"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 rounded-4 small mb-4">
                    <i class="material-icons fs-6 align-middle me-1">info</i>
                    Arahkan kamera ke QR Code yang disediakan panitia di lokasi event.
                </div>
                <div id="reader" class="rounded-4 overflow-hidden shadow-sm" style="background:#000;min-height:300px;"></div>
                <div id="scanner-status" class="text-center mt-3 fs-8 text-muted fst-italic">Meminta izin akses kamera...</div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light-dark w-100 rounded-pill fw-bold" data-bs-dismiss="modal" onclick="stopScanner()">Batalkan</button>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        background: white;
        padding: 22px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04);
        transition: transform .3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 18px;
        flex-shrink: 0;
    }

    .fs-9 {
        font-size: 0.75rem !important;
    }

    .fs-10 {
        font-size: 0.65rem !important;
    }

    .fs-1hx {
        font-size: 4rem;
    }

    .btn-white {
        background: white;
        color: var(--primary-color);
        border: none;
    }

    .btn-white:hover {
        background: #f8fafc;
    }

    /* Lamaran row (desktop table) — tema sama dengan wishlist */
    .lamaran-row {
        transition: all .25s ease;
    }

    .lamaran-row:hover td {
        background: #fff !important;
    }

    .lamaran-row:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    }

    /* Bungkus highlight border ke seluruh row pakai outline trick */
    .lamaran-row:hover td:first-child {
        border-left: 2px solid var(--primary-theme, #751e18) !important;
        border-radius: 12px 0 0 12px;
    }

    .lamaran-row:hover td:last-child {
        border-right: 2px solid var(--primary-theme, #751e18) !important;
        border-radius: 0 12px 12px 0;
    }

    .lamaran-row td {
        transition: background .2s ease, border-color .2s ease;
    }

    /* Lamaran card (mobile) */
    .lamaran-card {
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        background: #fff;
        transition: all .25s ease;
    }

    .lamaran-card:hover {
        border-color: var(--primary-theme, #751e18) !important;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06);
    }

    /* Wishlist card */
    .wishlist-card {
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        background: #fff;
        transition: all .25s ease;
    }

    .wishlist-card:hover {
        border-color: var(--primary-theme, #751e18) !important;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06);
    }
</style>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    // ── Clickable table rows ──
    document.querySelectorAll('.clickable-row').forEach(row => {
        row.addEventListener('click', function() {
            const href = this.dataset.href;
            if (href) window.location.href = href;
        });
    });

    // ── Toggle Lamaran / Wishlist ──
    function toggleSection(section) {
        const extras = document.querySelectorAll('.' + section + '-extra');
        const btn = document.getElementById('btn-toggle-' + section);
        const isHidden = extras[0]?.classList.contains('d-none');

        extras.forEach(el => el.classList.toggle('d-none', !isHidden));

        if (section === 'lamaran') {
            btn.textContent = isHidden ?
                'Sembunyikan' :
                'Lihat Semua ({{ $recentApplications->count() }})';
        } else {
            btn.textContent = isHidden ?
                'Sembunyikan' :
                'Lihat Semua ({{ $wishlistedJobs->count() }})';
        }
    }
</script>
@endpush
@endsection
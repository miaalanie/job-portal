@extends('layouts.frontend')

@section('title', 'Dashboard Pelamar - FindTalen')

@section('content')
<div class="pb-10 bg-white" style="min-height: 100vh;">
    <!-- Edge-to-Edge Header Banner -->
    <div class="bg-primary-theme">
        <div class="container-fluid px-0">
            <div class="pt-8 pb-16 px-6 px-md-15 mb-n10">
                <div class="container px-0 px-md-3">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-5 text-center text-md-start">
                        <div class="d-flex flex-column flex-md-row align-items-center">
                            <div class="border border-4 border-white border-opacity-30 shadow-lg rounded-circle mb-4 mb-md-0 me-md-6 overflow-hidden mx-auto" style="width: 110px; height: 110px; min-width: 110px; background: white;">
                                @if($pelamar->foto && $pelamar->foto != 'no-image')
                                <img src="{{ asset('storage/'.$pelamar->foto) }}" alt="user" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                <div class="text-primary-theme fw-bold fs-1 d-flex align-items-center justify-content-center w-100 h-100">{{ substr($pelamar->namalengkap, 0, 1) }}</div>
                                @endif
                            </div>
                            <div class="px-md-2">
                                <h1 class="fw-extrabold text-white mb-2 fs-1 ls-n1">Selamat Datang, {{ explode(' ', $pelamar->namalengkap)[0] }}! 👋</h1>
                                <p class="text-white text-opacity-85 mb-0 fs-7 mx-auto mx-md-0 lh-base" style="max-width: 500px;">Optimalkan pencarian kerjamu dan pantau status lamaran secara instan.</p>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-sm-row gap-3 w-100 w-md-auto px-4 px-md-0">
                            <button type="button" class="btn btn-white w-100 w-sm-auto px-6 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center h-50px transition-all hover-translate-y" onclick="startScanner()">
                                <i class="material-icons text-success me-2">qr_code_scanner</i> Scan Absensi
                            </button>
                            <a href="{{ route('frontend.events') }}" class="btn btn-warning w-100 w-sm-auto px-6 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center h-50px text-dark transition-all hover-translate-y">
                                <i class="material-icons me-2">explore</i> Cari Lowongan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-n6">

        <!-- Metrics Grid -->
        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-sm-6">
                <div class="stat-card h-100">
                    <div class="stat-icon bg-light-primary" style="background: rgba(13, 110, 253, 0.08); color: #0d6efd;">
                        <i class="material-icons">history</i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $totalEventsFollowed }}</h3>
                        <span class="fs-8 text-muted fw-medium ls-sm">Event Diikuti</span>
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
                        <span class="fs-8 text-muted fw-medium ls-sm">Event Akan Datang</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-12">
                <div class="stat-card h-100">
                    <div class="stat-icon bg-light-success" style="background: rgba(25, 135, 84, 0.08); color: #198754;">
                        <i class="material-icons">verified_user</i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h3 class="fw-bold mb-0">{{ $profileCompletion }}%</h3>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $profileCompletion }}%" aria-valuenow="{{ $profileCompletion }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="fs-9 text-muted fw-medium ls-sm mt-1 d-block">Kelengkapan Profil</span>
                    </div>
                </div>
            </div>
        </div>

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
                        <div class="p-4 rounded-4 border bg-white shadow-sm-hover transition-all h-100">
                            <div class="badge bg-danger bg-opacity-10 text-danger mb-2 fs-10 fw-bold px-2 py-1 rounded-pill">
                                {{ $lams->count() }} Lamaran Terdaftar
                            </div>
                            <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $even->namaperiode }}</h6>
                            <p class="text-muted fs-9 mb-3"><i class="material-icons fs-10 align-middle">calendar_today</i> {{ \Carbon\Carbon::parse($even->tanggalawal)->format('d M Y') }}</p>

                            <a href="{{ route('pelamar.print-card', encrypt($even->id)) }}" target="_blank" class="btn btn-sm btn-danger w-100 rounded-pill py-2 fw-bold shadow-sm">
                                <i class="material-icons fs-6 align-middle me-1">print</i> Cetak Kartu Peserta
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="row g-4">
            <!-- Recent Activity -->
            <div class="col-xl-8 col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0">Lamaran Terakhir</h5>
                            <a href="#" class="btn btn-sm btn-light fs-8 fw-bold px-3 rounded-pill text-primary-theme">Lihat Semua</a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border-0">
                                <thead class="bg-light fs-9 text-uppercase ls-1 text-muted">
                                    <tr>
                                        <th class="ps-3 border-0 py-3 rounded-start-3">Posisi & Perusahaan</th>
                                        <th class="border-0">Event Rekrutmen</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 text-center rounded-end-3">Hadir Dalam</th>
                                    </tr>
                                </thead>
                                <tbody class="border-0 mt-3">
                                    @forelse($recentApplications as $app)
                                    <tr class="mb-2">
                                        <td class="ps-3 border-0 py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px bg-light rounded-3 me-3 p-1 d-flex align-items-center justify-content-center shadow-sm">
                                                    @if($app->lowongan->register->perusahaan->logo)
                                                    <img src="{{ asset('storage/'.$app->lowongan->register->perusahaan->logo) }}" style="width: 32px; height: 32px; object-fit: contain;">
                                                    @else
                                                    <i class="material-icons text-muted fs-5">business</i>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="fw-bold text-dark fs-7 lh-1 mb-1 text-truncate">{{ $app->lowongan->namalowongan }}</div>
                                                    <div class="fs-8 text-muted text-truncate">{{ $app->lowongan->register->perusahaan->nama }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="border-0">
                                            <div class="fw-bold text-dark fs-8 mb-1 text-truncate">{{ $app->even->namaperiode }}</div>
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <span class="fs-9 text-muted">{{ \Carbon\Carbon::parse($app->even->tanggalawal)->format('d M Y') }}</span>
                                                @if($app->sesi)
                                                <span class="badge bg-light text-primary-theme fs-10 fw-bold border border-primary-theme border-opacity-10 rounded-pill px-2 py-0">Sesi: {{ $app->sesi->nama_sesi }} ({{ \Carbon\Carbon::parse($app->sesi->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($app->sesi->jam_selesai)->format('H:i') }})</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="border-0">
                                            @php
                                            $isHadir = $app->kehadirans->isNotEmpty();
                                            $evenDate = \Carbon\Carbon::parse($app->even->tanggalawal);
                                            $isToday = now()->isSameDay($evenDate);

                                            if ($isHadir) {
                                            $statusText = 'Hadir';
                                            $statusClass = 'bg-success';
                                            } else {
                                            $statusText = match($app->statusditerima) {
                                            '0' => 'Melamar',
                                            '2' => 'Wawancara',
                                            '3' => 'Diterima',
                                            default => 'Pending'
                                            };
                                            $statusClass = match($app->statusditerima) {
                                            '0' => 'bg-secondary',
                                            '2' => 'bg-primary',
                                            '3' => 'bg-success',
                                            default => 'bg-secondary'
                                            };
                                            }
                                            @endphp
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                <span class="badge {{ $statusClass }} bg-opacity-10 text-{{ str_replace('bg-', '', $statusClass) }} border border-{{ str_replace('bg-', '', $statusClass) }} border-opacity-25 rounded-pill px-3 py-2 fs-9">{{ $statusText }}</span>
                                                @if(!$isHadir && $isToday)
                                                <button type="button" class="btn btn-sm btn-light-success fw-bold fs-10 py-1 px-3 rounded-pill border border-success border-opacity-20 mt-1" onclick="startScanner()">
                                                    <i class="material-icons fs-9 align-middle">qr_code_scanner</i> Absen Sekarang
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="border-0 text-center">
                                            @php
                                            $evenDate = \Carbon\Carbon::parse($app->even->tanggalawal);
                                            $diff = round(now()->diffInDays($evenDate, false));
                                            @endphp
                                            @if($diff > 0)
                                            <div class="d-flex flex-column align-items-center">
                                                <span class="fw-extrabold text-primary-theme fs-6 lh-1">{{ $diff }}</span>
                                                <span class="text-muted fs-10 text-uppercase ls-1">Hari Lagi</span>
                                            </div>
                                            @elseif($diff == 0)
                                            <span class="badge bg-danger rounded-pill px-3 py-1 fs-9 sparkle-text animate-pulse">HARI INI</span>
                                            @else
                                            <span class="text-muted fs-9 opacity-50">Event Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="bg-light w-60px h-60px rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
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

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                                <i class="material-icons text-danger me-2">favorite</i> Lowongan Tersimpan (Wishlist)
                            </h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            @forelse($wishlistedJobs as $wish)
                            @php $loker = $wish->lowongan; @endphp
                            <div class="col-md-6">
                                <div class="wishlist-item p-3 border rounded-4 transition-all">
                                    <div class="d-flex gap-3 align-items-center">
                                        <div class="bg-light rounded-3 p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                                            @if($loker->register->perusahaan->logo)
                                            <img src="{{ asset('storage/'.$loker->register->perusahaan->logo) }}" class="img-fluid" style="max-height: 32px; object-fit: contain;">
                                            @else
                                            <i class="material-icons text-muted">business</i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="fw-bold text-dark fs-7 mb-0 text-truncate">{{ $loker->namalowongan }}</h6>
                                            <span class="fs-9 text-muted d-block text-truncate">{{ $loker->register->perusahaan->nama }}</span>
                                        </div>
                                        <div class="text-end">
                                            <a href="{{ route('vacancy.detail', encrypt($loker->id)) }}" class="btn btn-sm btn-icon btn-light rounded-circle shadow-sm">
                                                <i class="material-icons fs-6">chevron_right</i>
                                            </a>
                                        </div>
                                    </div>
                                    <hr class="my-3 opacity-10">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary-theme bg-opacity-10 text-primary-theme fs-10 px-2 py-1">{{ $loker->kategori->nama ?? 'Sektor Umum' }}</span>
                                            <span class="fs-10 text-muted d-flex align-items-center"><i class="material-icons fs-10 me-1">payments</i> {{ number_format($loker->gaji_awal / 1000000, 1) }} - {{ number_format($loker->gaji_akhir / 1000000, 1) }}jt</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="text-center py-4 bg-light rounded-4">
                                    <i class="material-icons fs-1 text-muted opacity-25 mb-2">favorite_border</i>
                                    <p class="text-muted small mb-0">Belum ada lowongan yang disimpan.</p>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Event Readiness Reminder -->
                @if($nextEvent)
                <div class="card border-0 shadow-sm rounded-4 bg-primary-theme text-white overflow-hidden mb-4">
                    <div class="card-body p-4 p-xl-5 position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-white bg-opacity-20 p-3 rounded-circle me-3">
                                        <i class="material-icons text-warning fs-3">campaign</i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0">Siapkan Diri Anda!</h4>
                                        <p class="opacity-75 mb-0 small text-uppercase ls-1">Event: <strong>{{ $nextEvent->namaperiode }}</strong></p>
                                    </div>
                                </div>
                                <div class="vstack gap-3 mb-2">
                                    <div class="d-flex align-items-center gap-3 bg-white bg-opacity-10 p-3 rounded-3 border border-white border-opacity-10">
                                        <i class="material-icons text-warning">checkroom</i>
                                        <div>
                                            <div class="fw-bold fs-7">Berpakaian Rapih & Formal</div>
                                            <div class="fs-9 opacity-75">Jas, kemeja, dan sepatu pantofel (Formal Office)</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 bg-white bg-opacity-10 p-3 rounded-3 border border-white border-opacity-10">
                                        <i class="material-icons text-warning">description</i>
                                        <div>
                                            <div class="fw-bold fs-7">Bawa Kelengkapan Dokumen</div>
                                            <div class="fs-9 opacity-75">Membawa CV fisik, KTP, dan sertifikat pendukung</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mt-4 mt-lg-0">
                                <div class="text-center p-4 rounded-4" style="background: rgba(255,255,255,0.05); border: 2px dashed rgba(255,255,255,0.2);">
                                    @php
                                    $diffDays = round(now()->diffInDays(\Carbon\Carbon::parse($nextEvent->tanggalawal), false));
                                    @endphp
                                    <h1 class="fw-extrabold mb-0" style="font-size: 3.5rem;">{{ $diffDays > 0 ? $diffDays : 'H' }}</h1>
                                    <p class="small text-uppercase ls-2 mb-0 opacity-75">{{ $diffDays > 0 ? 'Hari Lagi' : 'KONTEN HARI INI' }}</p>
                                </div>
                            </div>
                        </div>
                        <i class="material-icons position-absolute top-0 end-0 m-4 opacity-05 fs-1hx">event_available</i>
                    </div>
                </div>
                @else
                <!-- Profile Strength (Fallthrough) -->
                <div class="card border-0 shadow-sm rounded-4 bg-primary-theme text-white overflow-hidden mb-4">
                    <div class="card-body p-4 p-xl-5 d-flex flex-column flex-sm-row align-items-center justify-content-between position-relative">
                        <div class="d-flex align-items-center z-index-1">
                            <div class="bg-white bg-opacity-20 p-4 rounded-4 me-4 d-none d-md-flex item-center">
                                <i class="material-icons text-warning fs-1">tips_and_updates</i>
                            </div>
                            <div class="text-center text-sm-start">
                                <h4 class="fw-bold mb-1">Tingkatkan Peluang Kerjamu!</h4>
                                <p class="opacity-75 mb-0 small">Lengkapi Riwayat Pendidikan & Pengalaman untuk dilirik HRD.</p>
                            </div>
                        </div>
                        <a href="{{ route('pelamar.complete-data') }}" class="btn btn-warning px-5 py-3 fw-bold rounded-pill shadow-sm mt-4 mt-sm-0 z-index-1">Lengkapi CV</a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Sidebar -->
            <div class="col-xl-4 col-lg-5">
                
x   

                



                <!-- Event Invitation Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                    <div class="bg-primary-theme p-4 text-white text-center">
                        <h2 class="fw-bold mb-2">Job Fair 2024</h2>
                        <p class="small opacity-75 mb-3">Ikuti event rekrutmen massal dengan ribuan peluang karir.</p>
                        <a href="{{ route('frontend.events') }}" class="btn btn-white btn-sm px-4 fw-bold rounded-pill text-primary-theme">Eksplor Event</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Scanner Modal -->
<div class="modal fade" id="qrScannerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0">Scan QR Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="stopScanner()"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 rounded-4 small mb-4">
                    <i class="material-icons fs-6 align-middle me-1">info</i>
                    Arahkan kamera ke QR Code yang disediakan oleh panitia/perusahaan di lokasi event.
                </div>

                <div id="reader" class="rounded-4 overflow-hidden shadow-sm" style="background: #000; min-height: 300px;"></div>

                <div id="scanner-status" class="text-center mt-3 fs-8 text-muted italic">
                    Meminta izin akses kamera...
                </div>
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
        padding: 25px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 55px;
        height: 55px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
    }

    .fs-9 {
        font-size: 0.75rem;
    }

    .fs-10 {
        font-size: 0.65rem;
    }

    .ls-sm {
        letter-spacing: 0.2px;
    }

    .ls-1 {
        letter-spacing: 0.5px;
    }

    .btn-white {
        background: white;
        color: var(--primary-color);
        border: none;
    }

    .btn-white:hover {
        background: #f8fafc;
        transform: translateY(-2px);
    }

    .job-recommend-card {
        background: #f9fafb;
        border: 1px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .job-recommend-card:hover {
        background: white;
        border-color: rgba(127, 29, 29, 0.1);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.05);
        transform: translateX(5px);
    }

    .wishlist-item {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        transition: all 0.3s ease;
    }

    .wishlist-item:hover {
        border-color: var(--primary-theme) !important;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .max-w-150px {
        max-width: 150px;
    }

    .z-index-1 {
        z-index: 1;
    }

    .fs-1hx {
        font-size: 4rem;
    }
</style>
@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
@endpush
@endsection
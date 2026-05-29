@extends('layouts.frontend')

@section('title', 'Cari Lowongan Kerja Impian - ' . ($company->nama_perusahaan ?? 'FindTalen'))

@section('content')
<!-- Hero Section -->
<div class="bg-primary-theme position-relative overflow-hidden" style="padding-top: 90px !important; padding-bottom: 50px !important; background: linear-gradient(135deg, #7f1d1d 0%, #450a0a 100%) !important;">
    <!-- Animated background accents -->
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10 pointer-events-none">
        <div class="position-absolute top-0 start-0 bg-white rounded-circle" style="width: 300px; height: 300px; filter: blur(100px); transform: translate(-50%, -50%);"></div>
        <div class="position-absolute bottom-0 end-0 bg-warning rounded-circle" style="width: 250px; height: 250px; filter: blur(100px); transform: translate(50%, 50%);"></div>
    </div>
    
    <div class="container position-relative z-index-2">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 text-center text-lg-start">
                <div class="d-inline-flex align-items-center bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-20 px-4 py-2 mb-4 rounded-pill shadow-sm">
                    <span class="pulse-danger me-2"></span>
                    <span class="text-white fs-8 fw-bold ls-sm text-uppercase">
                        {{ $activeEvent ? 'EVENT AKTIF: ' . $activeEvent->namaperiode : 'TEMUKAN KARIR IMPIAN' }}
                    </span>
                </div>
                
                <h1 class="display-3 fw-extrabold text-white mb-4 lh-sm tracking-tight">
                    Cari Karir Impian <br>
                    <span class="text-warning">Bersama Kami.</span>
                </h1>
                
                <p class="fs-5 text-white text-opacity-80 mb-5 pe-lg-5 fw-light lh-lg">
                    {{ $activeEvent->visi ?? 'Temukan ribuan peluang kerja dari berbagai perusahaan ternama di seluruh Indonesia dalam satu platform terintegrasi.' }}
                </p>

                @if($activeEvent)
                <!-- Informative Event Logistics -->
                <div class="row g-4 mb-5">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center bg-white bg-opacity-10 backdrop-blur p-4 rounded-4 border border-white border-opacity-10 shadow-sm transition-all hover-translate-y h-100">
                            <div class="stat-icon bg-warning bg-opacity-20 text-warning me-3 mb-0" style="width: 50px; height: 50px;">
                                <i class="material-icons fs-4">calendar_today</i>
                            </div>
                            <div>
                                <span class="d-block fs-9 text-white opacity-60 text-uppercase fw-bold ls-1 mb-1">Tanggal & Waktu</span>
                                <span class="fw-bold text-white fs-6">
                                    {{ \Carbon\Carbon::parse($activeEvent->tanggalawal)->format('d M') }} - {{ \Carbon\Carbon::parse($activeEvent->tanggalselesai)->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center bg-white bg-opacity-10 backdrop-blur p-4 rounded-4 border border-white border-opacity-10 shadow-sm transition-all hover-translate-y h-100">
                            <div class="stat-icon bg-white bg-opacity-20 text-white me-3 mb-0" style="width: 50px; height: 50px;">
                                <i class="material-icons fs-4">place</i>
                            </div>
                            <div>
                                <span class="d-block fs-9 text-white opacity-60 text-uppercase fw-bold ls-1 mb-1">Tempat Pelaksanaan</span>
                                <span class="fw-bold text-white fs-6 line-clamp-1" title="{{ $activeEvent->lokasi }}">{{ $activeEvent->lokasi }}</span>
                                <span class="d-block fs-10 text-white opacity-50 text-truncate" style="max-width: 180px;">{{ $activeEvent->alamat_lengkap }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3 mb-5 mb-lg-0">
                    <a href="#vacancies" class="btn btn-warning px-5 py-3 fs-6 rounded-pill shadow-warning fw-bold d-flex align-items-center">
                        <i class="material-icons me-2">search</i> Jelajahi Lowongan
                    </a>
                    <a href="{{ route('perusahaan.register') }}" class="btn btn-outline-light px-5 py-3 fs-6 rounded-pill border-2 fw-bold d-none d-sm-flex align-items-center">
                        <i class="material-icons me-2">business</i> Mitra Perusahaan
                    </a>
                </div>
            </div>
            
            <div class="col-lg-5">
                <div class="position-relative py-5">
                    <!-- Glassmorphism Stats Overlay -->
                    <div class="position-absolute top-0 start-0 translate-middle-y mt-5 ms-n4 z-index-10 d-none d-md-block">
                        <div class="bg-white bg-opacity-90 backdrop-blur p-3 rounded-4 shadow-xl border border-white border-opacity-20 translate-y-3 animation-float">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary-theme p-2 rounded-3 text-white">
                                    <i class="material-icons fs-5">people_alt</i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-7 text-dark">{{ $stats['total_partners'] }}+</div>
                                    <div class="fs-10 text-muted">Mitra Perusahaan</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($activeEvent && $activeEvent->gambar)
                        <div class="hero-image-wrapper p-2 bg-white bg-opacity-10 rounded-5 border border-white border-opacity-20">
                            <img src="{{ asset('storage/'.$activeEvent->gambar) }}" class="img-fluid rounded-4 shadow-2xl skew-y-1" alt="Event Poster">
                        </div>
                    @else
                        <div class="hero-placeholder bg-white bg-opacity-10 backdrop-blur rounded-5 p-5 border border-white border-opacity-10 d-flex flex-column align-items-center justify-content-center text-center shadow-2xl" style="min-height: 400px;">
                            <i class="material-icons fs-10x text-white opacity-20 mb-3">campaign</i>
                            <h4 class="text-white fw-bold opacity-75">Update Karir Terbaru</h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @php $targetEvent = $headlineEvent ?: $activeEvent; @endphp
        
        @if($targetEvent && $targetEvent->sponsors->count() > 0)
        <!-- Event Sponsors: Infinite Parallax Scrolling -->
        <div class="mt-10 pt-10 overflow-hidden">
            <div class="d-flex align-items-center mb-5">
                <span class="fw-extrabold text-white opacity-90 text-uppercase fs-8 ls-2 me-4">Official Partners & Sponsors</span>
                <div class="flex-grow-1 border-top border-white opacity-20"></div>
            </div>
            
            <div class="sponsor-scroller-wrapper position-relative overflow-hidden pt-2 pb-2">
                <div class="sponsor-track d-flex align-items-center gap-4">
                    {{-- First Set --}}
                    @foreach($targetEvent->sponsors as $sponsor)
                        <div class="sponsor-logo-card bg-white bg-opacity-10 backdrop-blur rounded-4 p-3 flex-shrink-0 d-flex flex-column align-items-center justify-content-center border border-white border-opacity-20 shadow-sm transition-all hover-translate-y" style="width: 180px; height: 120px;">
                            @if($sponsor->logo)
                                <img src="{{ asset('storage/'.$sponsor->logo) }}" alt="{{ $sponsor->nama }}" class="img-fluid mb-2" style="max-height: 50px; transition: 0.3s;" title="{{ $sponsor->nama }}">
                            @else
                                <i class="material-icons text-white opacity-40 fs-2 mb-1">business</i>
                            @endif
                            <div class="text-white opacity-90 fw-bold fs-10 text-uppercase text-center line-clamp-1 w-100" title="{{ $sponsor->nama }}">
                                {{ Str::limit($sponsor->nama, 20) }}
                            </div>
                        </div>
                    @endforeach
                    
                    {{-- Duplicated Set for Infinite Loop (only if more than 3) --}}
                   @if(optional($targetEvent)->sponsors?->count() > 3)
                        @foreach($targetEvent->sponsors as $sponsor)
                            <div class="sponsor-logo-card bg-white bg-opacity-10 backdrop-blur rounded-4 p-3 flex-shrink-0 d-flex flex-column align-items-center justify-content-center border border-white border-opacity-20 shadow-sm transition-all d-none d-md-flex" style="width: 180px; height: 120px;">
                                @if($sponsor->logo)
                                    <img src="{{ asset('storage/'.$sponsor->logo) }}" alt="{{ $sponsor->nama }}" class="img-fluid mb-2" style="max-height: 50px; transition: 0.3s;">
                                @else
                                    <i class="material-icons text-white opacity-40 fs-2 mb-1">business</i>
                                @endif
                                <div class="text-white opacity-90 fw-bold fs-10 text-uppercase text-center line-clamp-1 w-100">
                                    {{ Str::limit($sponsor->nama, 20) }}
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .tracking-tight { letter-spacing: -1px; }
    .pulse-danger { width: 8px; height: 8px; background: #ffc107; border-radius: 50%; display: inline-block; animation: pulse 2s infinite; }
    @keyframes pulse { 0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); } 70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); } 100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); } }
    .animation-float { animation: float 6s ease-in-out infinite; }
    @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-20px); } 100% { transform: translateY(0px); } }
    .shadow-warning { box-shadow: 0 10px 25px -5px rgba(255, 193, 7, 0.4) !important; }
    .hero-image-wrapper img { transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .hero-image-wrapper:hover img { transform: scale(1.02) translateY(-10px); }
</style>

<!-- Stats Mini-Section (Under Hero) -->
<div class="bg-white position-relative z-index-10" style="margin-top: -50px;">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-10">
                <div class="bg-white rounded-5 shadow-2xl p-4 border border-light d-flex flex-column flex-md-row align-items-center justify-content-around text-center gap-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-light-danger p-3 rounded-circle text-primary-theme">
                            <i class="material-icons">business</i>
                        </div>
                        <div class="text-start">
                            <h4 class="fw-bold mb-0 text-dark">{{ $stats['total_partners'] }}</h4>
                            <p class="text-muted mb-0 fs-10 text-uppercase fw-bold ls-1">Perusahaan Mitra</p>
                        </div>
                    </div>
                    <div class="vr d-none d-md-block"></div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-light-success p-3 rounded-circle text-success" style="background: rgba(25, 135, 84, 0.08);">
                            <i class="material-icons">work_outline</i>
                        </div>
                        <div class="text-start">
                            <h4 class="fw-bold mb-0 text-dark">{{ $stats['total_vacancies'] }}</h4>
                            <p class="text-muted mb-0 fs-10 text-uppercase fw-bold ls-1">Lowongan Tersedia</p>
                        </div>
                    </div>
                    <div class="vr d-none d-md-block"></div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-light-info p-3 rounded-circle text-info" style="background: rgba(13, 110, 253, 0.08);">
                            <i class="material-icons">verified_user</i>
                        </div>
                        <div class="text-start">
                            <h4 class="fw-bold mb-0 text-dark">{{ $stats['events_count'] }}</h4>
                            <p class="text-muted mb-0 fs-10 text-uppercase fw-bold ls-1">Total Agenda Event</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .ls-1 { letter-spacing: 1px; }
    .backdrop-blur { backdrop-filter: blur(10px); }
    
    /* Sponsor Infinite Scroll Animation */
    @if($targetEvent->sponsors->count() > 3)
    .sponsor-track {
        animation: scroll 30s linear infinite;
        width: calc(200px * {{ $targetEvent->sponsors->count() * 2 }});
    }
    .sponsor-track:hover {
        animation-play-state: paused;
    }
    @keyframes scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(calc(-200px * {{ $targetEvent->sponsors->count() }})); }
    }
    @endif
    
    .custom-scrollbar::-webkit-scrollbar { height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
</style>

<!-- Latest Vacancies -->
<section id="vacancies" class="py-5" style="background:#fffafa">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark fs-1 mb-3">Peluang Karir <span class="text-primary-theme">Terbaik</span></h2>
            <div class="mx-auto bg-primary-theme mb-4" style="width: 80px; height: 5px; border-radius: 10px;"></div>
            <p class="text-muted fs-5">Raih kesempatan emas dari berbagai industri terkemuka</p>
        </div>
        
        <div class="position-relative pt-2 pb-5 px-md-5">
            <!-- Enhanced Navigation Buttons -->
            <button class="btn btn-white shadow-lg rounded-circle position-absolute start-0 top-50 translate-middle-y z-index-50 d-none d-md-flex align-items-center justify-content-center transition-all hover-translate-x-n3" id="scroll-prev" style="width: 55px; height: 55px; margin-left: -5px; border: none; background: #fff !important; color: #7f1d1d;">
                <i class="material-icons fs-1 fw-bold">chevron_left</i>
            </button>
            <button class="btn btn-white shadow-lg rounded-circle position-absolute end-0 top-50 translate-middle-y z-index-50 d-none d-md-flex align-items-center justify-content-center transition-all hover-translate-x-3" id="scroll-next" style="width: 55px; height: 55px; margin-right: -5px; border: none; background: #fff !important; color: #7f1d1d;">
                <i class="material-icons fs-1 fw-bold">chevron_right</i>
            </button>

            <div class="vacancy-scroll-container d-flex align-items-stretch gap-4 overflow-auto pb-4 custom-scrollbar" id="vacancy-container" style="scroll-snap-type: x mandatory; scroll-behavior: smooth;">
                @forelse($vacancies as $job)
                <div class="flex-shrink-0" style="width: 380px; scroll-snap-align: start;">
                    <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition-all" style="overflow: hidden;">
                        <div class="p-1 bg-primary-theme opacity-75"></div>
                        <div class="card-body p-4 pt-5">
                            <div class="d-flex align-items-start mb-4">
                                <div class="stat-icon bg-light text-primary-theme me-3 shadow-sm rounded-circle" style="min-width: 55px; height: 55px; background: rgba(127, 29, 29, 0.05) !important;">
                                    @php $perusahaan = $job->register->perusahaan; @endphp
                                    @if($perusahaan->logo)
                                        <img src="{{ asset('storage/'.$perusahaan->logo) }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: contain;">
                                    @else
                                        <i class="material-icons">business</i>
                                    @endif
                                </div>
                                <div class="d-flex flex-column">
                                    <h5 class="text-dark fw-bold mb-1 lh-sm">{{ $job->namalowongan }}</h5>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted fw-medium fs-7">{{ $perusahaan->nama }}</span>
                                        @if($job->register && $job->register->even)
                                            <span class="badge bg-light-danger text-danger fs-9 fw-bold px-2 py-1 rounded-1 text-uppercase">
                                                {{ $job->register->even->namaperiode }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <span class="badge bg-light text-danger-emphasis px-3 py-2 rounded-pill fs-8 fw-semibold" style="color: #7f1d1d !important; background: rgba(127, 29, 29, 0.05) !important;">
                                    <i class="material-icons fs-9 align-middle me-1">place</i> {{ $job->kategorilokasi }}
                                </span>
                                <span class="badge bg-light text-success-emphasis px-3 py-2 rounded-pill fs-8 fw-semibold">
                                    <i class="material-icons fs-9 align-middle me-1">payments</i> 
                                    Rp {{ number_format($job->gaji_awal, 0, ',', '.') }} - {{ number_format($job->gaji_akhir, 0, ',', '.') }}
                                </span>
                            </div>
                            
                            <p class="text-muted fs-7 line-clamp-3 mb-4">
                                {{ Str::limit(strip_tags($job->deskripsi), 120) }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light">
                                <span class="text-muted fs-8 d-flex align-items-center"><i class="material-icons fs-8 me-1 text-primary-theme opacity-75">update</i> Baru Saja</span>
                                <a href="{{ route('vacancy.detail', encrypt($job->id)) }}" class="btn btn-outline-danger btn-sm px-4 rounded-pill fw-bold border-2 hover-bg-red transition-all">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="w-100 text-center py-5">
                    <div class="bg-white rounded-5 p-5 shadow-sm border border-light mx-auto" style="max-width: 600px;">
                        <i class="material-icons fs-10x text-muted opacity-25 mb-4">search_off</i>
                        <h4 class="fw-bold mb-2">Belum ada lowongan</h4>
                        <p class="text-muted mb-0">Silakan cek kembali dalam beberapa saat.</p>
                    </div>
                </div>
                @endforelse
            </div>
            
            @if($vacancies->count() > 0 && $activeEvent)
            <div class="text-center mt-5 pt-3">
                <a href="{{ route('frontend.event.vacancies', encrypt($activeEvent->id)) }}" class="btn btn-primary-theme px-5 py-3 rounded-pill shadow-lg fw-bold d-inline-flex align-items-center transition-all hover-translate-y">
                    <i class="material-icons me-2">explore</i> Lihat Semua Lowongan di Event Ini
                </a>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Footer Extra Branding -->
@if($activeEvent && $activeEvent->alamat_lengkap)
<section id="location" class="py-5 bg-white">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0 pe-lg-5">
                <span class="text-primary-theme fw-bold mb-2 d-block">LOKASI PELAKSANAAN</span>
                <h2 class="fw-bold mb-4 fs-1 text-dark">Ayo Datang & <span class="text-primary-theme">Bergabung!</span></h2>
                <p class="text-muted fs-5 mb-5 pe-lg-5 lh-lg">Kami mengundang Anda untuk hadir langsung di lokasi pelaksanaan Job Fair. Pastikan Anda sudah melengkapi profil digital Anda sebelum hadir untuk mempermudah proses lamaran.</p>
                <div class="d-flex align-items-start mb-4 p-4 rounded-4 bg-light border-start border-4 border-danger">
                    <div class="stat-icon bg-white text-primary-theme me-4 shadow-sm">
                        <i class="material-icons">place</i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 fs-4">{{ $activeEvent->lokasi }}</h5>
                        <p class="text-muted mb-0 fs-6">{{ $activeEvent->alamat_lengkap }}</p>
                    </div>
                </div>
                @if($activeEvent->latitude && $activeEvent->longitude)
                <a href="https://www.google.com/maps/search/?api=1&query={{ $activeEvent->latitude }},{{ $activeEvent->longitude }}" target="_blank" class="btn btn-theme px-5 py-3 mt-3">
                    <i class="material-icons align-middle me-2">map</i> Petunjuk Arah
                </a>
                @endif
            </div>
            <div class="col-lg-6">
                <div class="position-relative">
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-primary-theme opacity-10 rounded-4 rotate-3"></div>
                    @if($activeEvent->gambar_layout)
                        <img src="{{ asset('storage/'.$activeEvent->gambar_layout) }}" class="img-fluid rounded-4 shadow-lg border-0 position-relative z-index-1" alt="Layout Event">
                    @else
                        <div class="bg-light p-5 rounded-4 text-center border-dashed position-relative z-index-1" style="min-height: 350px; display: flex; flex-direction: column; justify-content: center;">
                            <i class="material-icons fs-5x opacity-25 text-muted">map</i>
                            <p class="text-muted mt-3 mb-0 fs-5 fw-medium">Denah Lokasi Segera Tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<style>
    .z-index-1 { z-index: 1; }
    .rotate-3 { transform: rotate(3deg); transition: 0.5s; pointer-events: none; }
    .transition-all { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .hover-shadow-lg:hover { transform: translateY(-10px); box-shadow: 0 25px 50px -12px rgba(225, 29, 72, 0.15) !important; }
    .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    html { scroll-behavior: smooth; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('vacancy-container');
    const nextBtn = document.getElementById('scroll-next');
    const prevBtn = document.getElementById('scroll-prev');

    if (container && nextBtn && prevBtn) {
        const scrollAmount = 400; // Width of card + gap

        nextBtn.addEventListener('click', () => {
            container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        prevBtn.addEventListener('click', () => {
            container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        // Optional: Hide buttons if at start/end
        container.addEventListener('scroll', () => {
            const atStart = container.scrollLeft <= 0;
            const atEnd = container.scrollLeft + container.clientWidth >= container.scrollWidth - 1;
            
            prevBtn.style.opacity = atStart ? '0.3' : '1';
            prevBtn.style.pointerEvents = atStart ? 'none' : 'auto';
            
            nextBtn.style.opacity = atEnd ? '0.3' : '1';
            nextBtn.style.pointerEvents = atEnd ? 'none' : 'auto';
        });

        // Trigger once on load
        const atStart = container.scrollLeft <= 0;
        prevBtn.style.opacity = atStart ? '0.3' : '1';
        prevBtn.style.pointerEvents = atStart ? 'none' : 'auto';
    }
});
</script>
@endsection

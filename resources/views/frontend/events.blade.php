@extends('layouts.frontend')

@section('title', 'Daftar Event Rekrutmen - ' . ($company->nama_perusahaan ?? 'FindTalen'))

@section('content')
<!-- Hero Section -->
<div class="bg-primary-theme py-20 position-relative overflow-hidden" style="padding-top: 160px !important; padding-bottom: 100px !important;">
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-5" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
    <div class="container text-center text-white position-relative z-index-1">
        <h1 class="display-3 fw-extrabold mb-4 ls-n1">Agenda <span class="text-warning">Event</span> Karir</h1>
        <p class="fs-5 opacity-80 mx-auto fw-medium mb-10" style="max-width: 750px; line-height: 1.8;">
            Temukan ribuan peluang kerja melalui puluhan event Job Fair dan bursa kerja massal.
        </p>

        <!-- Search Feature -->
        <div class="max-w-600mx-auto mb-15">
            <form action="{{ route('frontend.events') }}" method="GET">
                <div class="input-group p-2 bg-white rounded-pill shadow-lg overflow-hidden border-2 border-white focus-within-primary transition-all">
                    <span class="input-group-text bg-transparent border-0 ps-4">
                        <i class="material-icons text-primary-theme">search</i>
                    </span>
                    <input type="text" name="q" class="form-control border-0 shadow-none fs-7 py-3" placeholder="Cari Nama Event atau Lokasi..." value="{{ $q ?? '' }}">
                    <button class="btn btn-theme px-8 rounded-pill fw-bold ls-1" type="submit">CARI</button>
                </div>
            </form>
            @if($q)
                <div class="mt-4 fs-8 opacity-75">
                    Menampilkan hasil pencarian untuk: <span class="fw-bold text-warning text-decoration-underline">"{{ $q }}"</span> 
                    <a href="{{ route('frontend.events') }}" class="ms-2 text-white text-decoration-none border-bottom border-white border-opacity-50 hover-opacity-100 italic">Bersihkan filter</a>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="container py-10 mt-n10 position-relative z-index-2">
    <div class="row g-8">
        @forelse($events as $event)
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card h-100 border-0 shadow-lg-hover rounded-5 overflow-hidden transition-all hover-translate-y-n3 bg-white">
                    <div class="position-relative">
                        {{-- Event Image --}}
                        <div class="event-card-img-wrapper">
                            @if($event->gambar)
                                <img src="{{ asset('storage/'.$event->gambar) }}" class="card-img-top event-img" alt="{{ $event->namaperiode }}">
                            @else
                                <div class="bg-light-subtle d-flex align-items-center justify-content-center event-img">
                                    <i class="material-icons fs-1 text-muted opacity-25">event</i>
                                </div>
                            @endif
                            <div class="overlay-gradient"></div>
                        </div>

                        {{-- Event Status Badge --}}
                        <div class="position-absolute top-0 end-0 p-4">
                            @php
                                $isUpcoming = \Carbon\Carbon::parse($event->tanggalawal)->isFuture();
                                $isActive = now()->between($event->tanggalawal, $event->tanggalselesai);
                            @endphp
                            @if($isActive)
                                <span class="badge badge-glow bg-success px-4 py-2 fs-9 fw-bold ls-1">SEDANG BERJALAN</span>
                            @elseif($isUpcoming)
                                <span class="badge badge-glow bg-primary-theme px-4 py-2 fs-9 fw-bold ls-1">MENDATANG</span>
                            @else
                                <span class="badge bg-secondary opacity-75 px-4 py-2 fs-9 fw-bold ls-1">BERAKHIR</span>
                            @endif
                        </div>

                        {{-- Date Label --}}
                        <div class="position-absolute bottom-0 start-0 p-4">
                            <div class="d-flex align-items-center bg-white rounded-3 px-3 py-2 shadow-sm">
                                <i class="material-icons fs-6 text-primary-theme me-2">calendar_month</i>
                                <span class="text-dark fw-bold fs-9">{{ \Carbon\Carbon::parse($event->tanggalawal)->format('d M') }} - {{ \Carbon\Carbon::parse($event->tanggalselesai)->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-6 d-flex flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fw-extrabold text-dark mb-4 lh-base">{{ $event->namaperiode }}</h4>
                            
                            {{-- Info List --}}
                            <div class="mb-5">
                                <div class="d-flex align-items-center mb-3 text-gray-700 fs-8">
                                    <div class="symbol symbol-30px me-3 bg-light rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="material-icons fs-7 text-danger">place</i>
                                    </div>
                                    <span class="text-truncate" style="max-width: 250px;">{{ $event->lokasi }}</span>
                                </div>
                                
                                <div class="d-flex align-items-center mb-3 text-gray-700 fs-8">
                                    <div class="symbol symbol-30px me-3 bg-light rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="material-icons fs-7 text-primary-theme">business_center</i>
                                    </div>
                                    <span class="fw-bold text-dark">{{ $event->lowongans_count }}</span>&nbsp;Lowongan Tersedia
                                </div>
                            </div>
                            
                            {{-- Quote / Visi --}}
                            <div class="p-4 bg-light rounded-4 mb-6 border-start border-4 border-primary-theme">
                                <p class="text-muted fs-8 italic mb-0 line-clamp-2">"{{ $event->visi }}"</p>
                            </div>
                        </div>
                        
                        <div class="d-grid mt-auto">
                            <a href="{{ route('frontend.event.vacancies', encrypt($event->id)) }}" class="btn btn-theme py-3 rounded-pill fw-bold ls-1 shadow-sm-hover d-flex align-items-center justify-content-center">
                                JELAJAHI LOWONGAN <i class="material-icons fs-6 ms-2">arrow_forward</i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-20">
                <div class="bg-white rounded-5 p-10 shadow-lg max-w-600mx-auto">
                    <div class="symbol symbol-100px mb-6">
                        <span class="symbol-label bg-light-danger rounded-circle">
                            <i class="material-icons fs-3tx text-danger">upcoming</i>
                        </span>
                    </div>
                    <h2 class="fw-extrabold text-dark mb-3">Agenda Masih Kosong</h2>
                    <p class="text-muted fs-6">Saat ini belum ada event rekrutmen terbaru. Nantikan informasi selanjutnya dari tim FindTalen untuk agenda Job Fair mendatang!</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($events->hasPages())
        <div class="d-flex justify-content-center mt-15">
            <div class="bg-white p-3 rounded-pill shadow-sm">
                {{ $events->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<style>
    .mt-n10 { margin-top: -5rem !important; }
    .z-index-2 { z-index: 2; }
    .ls-1 { letter-spacing: 0.5px; }
    .ls-n1 { letter-spacing: -1px; }
    .fs-9 { font-size: 0.75rem; }
    .fs-8 { font-size: 0.85rem; }
    .fw-extrabold { font-weight: 800; }
    .italic { font-style: italic; }
    .rounded-5 { border-radius: 1.25rem !important; }
    .max-w-600mx-auto { max-width: 600px; margin: 0 auto; }
    
    .shadow-lg-hover { transition: box-shadow 0.3s; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .shadow-lg-hover:hover { box-shadow: 0 20px 50px rgba(0,0,0,0.12) !important; }
    
    .hover-translate-y-n3:hover { transform: translateY(-8px); }
    
    .event-card-img-wrapper { position: relative; height: 220px; overflow: hidden; }
    .event-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .card:hover .event-img { transform: scale(1.08); }
    
    .overlay-gradient { 
        position: absolute; bottom: 0; left: 0; width: 100%; height: 60%; 
        background: linear-gradient(to top, rgba(0,0,0,0.4) 0%, transparent 100%); 
    }
    
    .badge-glow { box-shadow: 0 0 15px currentColor; }
    
    .symbol-30px { width: 30px; height: 30px; }
    .symbol-100px { width: 100px; height: 100px; margin: 0 auto; }
    
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    
    @media (max-width: 767.98px) {
        .display-3 { font-size: 2.5rem; }
        .mt-n10 { margin-top: -3rem !important; }
    }
</style>
@endsection

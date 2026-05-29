@extends('layouts.frontend')

@section('title', 'Lowongan di ' . $event->namaperiode . ' - FindTalen')

@section('content')
<!-- Page Header -->
<div class="bg-primary-theme py-5 position-relative overflow-hidden" style="padding-top: 150px !important; padding-bottom: 60px !important;">
    <div class="container text-white">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white opacity-75">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('frontend.events') }}" class="text-white opacity-75">Event</a></li>
                        <li class="breadcrumb-item active text-white fw-bold" aria-current="page">Lowongan</li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold mb-2">{{ $event->namaperiode }}</h1>
                <p class="fs-5 opacity-75 mb-0"><i class="material-icons fs-5 align-middle me-1">work_outline</i> Menampilkan {{ $vacancies->total() }} peluang karir aktif</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <div class="bg-white bg-opacity-10 backdrop-blur rounded-4 p-3 d-inline-block border border-white border-opacity-10">
                    <div class="d-flex align-items-center text-start">
                        <div class="stat-icon bg-white bg-opacity-20 text-white me-3 mb-0" style="width: 45px; height: 45px;">
                            <i class="material-icons fs-5">place</i>
                        </div>
                        <div>
                            <span class="d-block fs-8 opacity-75 text-uppercase fw-bold ls-1">Lokasi Pelaksanaan</span>
                            <span class="fw-bold">{{ $event->lokasi }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="fw-bold text-dark mb-0">Filter Pencarian</h5>
                        <a href="{{ route('frontend.event.vacancies', encrypt($event->id)) }}" class="text-primary-theme fs-8 fw-bold text-decoration-none">Reset</a>
                    </div>
                    
                    <form action="{{ route('frontend.event.vacancies', encrypt($event->id)) }}" method="GET">
                        <!-- Dropdown Event (Quick Switch) -->
                        <div class="mb-4">
                            <label class="form-label fs-8 fw-bold text-uppercase text-muted ls-1">Ganti Event</label>
                            <select id="event_switcher" class="form-select form-select-sm bg-light-primary border-primary-theme-subtle rounded-3 fw-bold">
                                @foreach($events as $e)
                                    <option value="{{ route('frontend.event.vacancies', encrypt($e->id)) }}" {{ $event->id == $e->id ? 'selected' : '' }}>
                                        {{ $e->namaperiode }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text fs-9 mt-1 text-primary-theme opacity-75">Lihat lowongan dari event lain.</div>
                        </div>

                        <hr class="my-4 opacity-5">

                        <!-- Search by Company -->
                        <div class="mb-4">
                            <label class="form-label fs-8 fw-bold text-uppercase text-muted ls-1">Perusahaan</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0"><i class="material-icons fs-6 text-muted">business</i></span>
                                <input type="text" name="perusahaan" class="form-control bg-light border-0" placeholder="Cari perusahaan..." value="{{ request('perusahaan') }}">
                            </div>
                        </div>

                        <!-- Dropdown Category -->
                        <div class="mb-4">
                            <label class="form-label fs-8 fw-bold text-uppercase text-muted ls-1">Kategori Lowongan</label>
                            <select name="kategori" class="form-select form-select-sm bg-light border-0 rounded-3">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('kategori') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dropdown Location -->
                        <div class="mb-4">
                            <label class="form-label fs-8 fw-bold text-uppercase text-muted ls-1">Kategori Lokasi</label>
                            <select name="lokasi" class="form-select form-select-sm bg-light border-0 rounded-3">
                                <option value="">Semua Lokasi</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc }}" {{ request('lokasi') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Salary Range -->
                        <div class="mb-5">
                            <label class="form-label fs-8 fw-bold text-uppercase text-muted ls-1">Gaji Minimum s/d</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0 text-muted fs-8">Rp</span>
                                <input type="number" name="gaji_max" class="form-control bg-light border-0" placeholder="Contoh: 5000000" value="{{ request('gaji_max') }}">
                            </div>
                            <div class="form-text fs-9 mt-2 text-muted">Menampilkan lowongan dengan gaji awal di bawah nilai ini.</div>
                        </div>

                        <button type="submit" class="btn btn-theme w-100 py-3 rounded-pill fw-bold">Terapkan Filter</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Vacancy List -->
        <div class="col-lg-9">
            <div class="row g-4">
                @forelse($vacancies as $job)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition-all card-vacancy">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-4">
                                <div class="symbol symbol-45px me-3">
                                    @if($job->register->perusahaan->logo)
                                        <img src="{{ asset('storage/'.$job->register->perusahaan->logo) }}" class="rounded-circle p-1 bg-white shadow-sm" style="width: 45px; height: 45px; object-fit: contain;">
                                    @else
                                        <div class="bg-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <i class="material-icons text-muted">business</i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-1 lh-sm line-clamp-1 text-uppercase fs-7 ls-sm">{{ $job->namalowongan }}</h6>
                                    <span class="text-muted fs-8 fw-medium">{{ $job->register->perusahaan->nama }}</span>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2 mb-4">
                                <div class="d-flex align-items-center text-muted fs-8">
                                    <i class="material-icons fs-7 me-1 text-danger opacity-75">place</i>
                                    <span>{{ $job->kategorilokasi }}</span>
                                </div>
                                <div class="d-flex align-items-center text-muted fs-8">
                                    <i class="material-icons fs-7 me-1 text-success opacity-75">payments</i>
                                    <span class="fw-bold text-dark">Rp {{ number_format($job->gaji_awal, 0, ',', '.') }}++</span>
                                </div>
                            </div>

                            <p class="text-muted fs-8 line-clamp-3 mb-4">
                                {{ Str::limit(strip_tags($job->deskripsi), 80) }}
                            </p>

                            <div class="pt-3 border-top border-light d-grid">
                                <a href="{{ route('vacancy.detail', encrypt($job->id)) }}" class="btn btn-outline-danger btn-sm rounded-pill fw-bold border-2">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <div class="bg-white rounded-5 p-5 shadow-sm border border-light">
                        <i class="material-icons fs-10x text-muted opacity-25 mb-4">search_off</i>
                        <h4 class="fw-bold mb-2">Belum ada lowongan</h4>
                        <p class="text-muted mb-4">Coba sesuaikan filter pencarian Anda untuk mendapatkan hasil yang lebih banyak.</p>
                        <a href="{{ route('frontend.event.vacancies', encrypt($event->id)) }}" class="btn btn-theme px-5 py-3 mt-3">Reset Semua Filter</a>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $vacancies->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
    .backdrop-blur { backdrop-filter: blur(10px); }
    .ls-sm { letter-spacing: 0.2px; }
    .card-vacancy { border-left: 3px solid transparent !important; }
    .card-vacancy:hover { border-left-color: var(--primary-theme) !important; transform: translateY(-5px); }
    .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
    .form-select-sm, .form-control-sm { border-radius: 8px; }
    .pagination .page-link { border-radius: 8px; margin: 0 3px; border: none; font-weight: 600; color: #374151; }
    .pagination .page-item.active .page-link { background-color: var(--primary-theme); color: #fff; }
    .bg-light-primary { background-color: rgba(127, 29, 29, 0.05) !important; }
    .border-primary-theme-subtle { border: 1px solid rgba(127, 29, 29, 0.2) !important; }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#event_switcher').on('change', function() {
            const nextUrl = $(this).val();
            if (nextUrl) {
                window.location.href = nextUrl;
            }
        });
    });
</script>
@endpush
@endsection

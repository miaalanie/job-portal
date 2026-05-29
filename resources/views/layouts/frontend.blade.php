<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ($company->nama_perusahaan ?? 'FindTalen') . ' - Cari Lowongan Kerja Impian')</title>
    
    @if($company && $company->favicon)
        <link rel="icon" type="image/png" href="{{ asset($company->favicon) }}">
    @endif
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        :root {
            --primary-color: {{ $company->primary_color ?? '#7f1d1d' }};
            --secondary-color: {{ $company->secondary_color ?? '#111827' }};
            --bg-light: #fff5f5;
            --text-dark: #111827;
        }
    </style>
    <link href="{{ asset('css/frontend.min.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg py-3 sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                @if($company && $company->logo)
                    <img src="{{ asset($company->logo) }}" height="40" class="me-2" alt="Logo">
                    <span class="fw-bold fs-4 text-primary-theme">{{ $company->nama_perusahaan }}</span>
                @else
                    <span class="fw-extrabold fs-3 text-primary-theme">
                        @php
                            $nameParts = explode(' ', $company->nama_perusahaan ?? 'Find Talen', 2);
                        @endphp
                        {{ $nameParts[0] }}@if(isset($nameParts[1]))<span class="text-warning">{{ $nameParts[1] }}</span>@endif
                    </span>
                @endif
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="material-icons">menu</i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('events*') ? 'active' : '' }}" href="{{ route('frontend.events') }}">{{ __('Lowongan') }}</a>
                    </li>

                    @guest
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle {{ request()->is('register-*') ? 'active' : '' }}" href="#" id="navbarRegister" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('Daftar') }}
                            </a>
                            <ul class="dropdown-menu border-0 shadow-xl p-3 rounded-4 mt-2" aria-labelledby="navbarRegister" style="min-width: 200px;">
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 fw-medium d-flex align-items-center mb-1" href="{{ route('perusahaan.register') }}">
                                        <span class="material-icons fs-5 me-2 text-primary-theme">business</span> {{ __('Perusahaan') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 fw-medium d-flex align-items-center" href="{{ route('pelamar.register') }}">
                                        <span class="material-icons fs-5 me-2 text-success">person</span> {{ __('Pelamar') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item ms-lg-4 mt-3 mt-lg-0">
                            <a href="{{ route('login') }}" class="btn btn-theme px-5 py-2 shadow-none rounded-3">{{ __('Masuk') }}</a>
                        </li>
                    @else
                        <li class="nav-item dropdown ms-lg-4 mt-3 mt-lg-0">
                            <a class="nav-link dropdown-toggle d-flex align-items-center bg-light rounded-pill px-4 py-2 text-primary-theme fw-bold" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="material-icons me-2">account_circle</i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-xl p-3 rounded-4 mt-2" aria-labelledby="userDropdown" style="min-width: 220px;">
                                <li class="px-3 py-2 mb-2 border-bottom">
                                    <div class="fw-bold text-dark fs-7">{{ Auth::user()->name }}</div>
                                    <div class="fs-9 text-muted">{{ Auth::user()->email }}</div>
                                </li>
                                <li>
                                    @php
                                        $dashboardRoute = Auth::user()->hasRole('Pelamar') ? route('pelamar.dashboard') : '/admin/dashboard';
                                    @endphp
                                    <a class="dropdown-item rounded-3 py-2 fw-medium d-flex align-items-center" href="{{ $dashboardRoute }}">
                                        <span class="material-icons fs-5 me-2 text-primary-theme">dashboard</span> Dashboard
                                    </a>
                                </li>
                                @if(Auth::user()->hasRole('Pelamar'))
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 fw-medium d-flex align-items-center" href="{{ route('home') }}">
                                        <span class="material-icons fs-5 me-2 text-info">search</span> Cari Lowongan
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 fw-medium d-flex align-items-center" href="{{ route('pelamar.complete-data') }}">
                                        <span class="material-icons fs-5 me-2 text-warning">assignment_ind</span> Lengkapi Data Diri
                                    </a>
                                </li>
                                @endif
                                <li><hr class="dropdown-divider opacity-10"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item rounded-3 py-2 fw-medium d-flex align-items-center text-danger">
                                            <span class="material-icons fs-5 me-2">logout</span> Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="py-15 py-lg-20 px-4 px-lg-0">
        <div class="container">
            <div class="row g-8 g-lg-10">
                <div class="col-lg-4 mb-10 mb-lg-0">
                    <h4 class="text-white fw-bold mb-6 fs-2">{{ $company->nama_perusahaan ?? 'FindTalen' }}</h4>
                    <p class="lh-lg opacity-75 mb-8 pe-lg-10">{{ $company->deskripsi ?? 'Platform rekrutmen terintegrasi untuk membantu pencari kerja menemukan karir impian mereka.' }}</p>
                    <div class="d-flex gap-4">
                        @if($company->fb)
                            <a href="{{ $company->fb }}" target="_blank" class="btn btn-icon btn-light btn-sm rounded-circle shadow-sm hover-elevate-up"><i class="material-icons fs-5">facebook</i></a>
                        @endif
                        @if($company->ig)
                            <a href="{{ $company->ig }}" target="_blank" class="btn btn-icon btn-light btn-sm rounded-circle shadow-sm hover-elevate-up"><i class="material-icons fs-5">alternate_email</i></a>
                        @endif
                        @if($company->website)
                            <a href="{{ $company->website }}" target="_blank" class="btn btn-icon btn-light btn-sm rounded-circle shadow-sm hover-elevate-up"><i class="material-icons fs-5">language</i></a>
                        @endif
                    </div>
                </div>
                
                <div class="col-sm-6 col-lg-2 mb-10 mb-lg-0 ps-lg-12">
                    <h5 class="text-white fw-bold mb-6">Tautan Cepat</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-4"><a href="/" class="text-decoration-none transition-link">Beranda</a></li>
                        <li class="mb-4"><a href="{{ route('frontend.events') }}" class="text-decoration-none transition-link">Daftar Lowongan</a></li>
                        <li class="mb-4"><a href="#" class="text-decoration-none transition-link">Tentang Kami</a></li>
                        <li class="mb-4"><a href="#" class="text-decoration-none transition-link">Bantuan & FAQ</a></li>
                    </ul>
                </div>

                <div class="col-sm-6 col-lg-2 mb-10 mb-lg-0">
                    <h5 class="text-white fw-bold mb-6">Perusahaan</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-4"><a href="{{ route('login') }}" class="text-decoration-none transition-link">Login Mitra</a></li>
                        <li class="mb-4"><a href="{{ route('perusahaan.register') }}" class="text-decoration-none transition-link">Daftar Perusahaan</a></li>
                        <li class="mb-4"><a href="#" class="text-decoration-none transition-link">Kebijakan Karir</a></li>
                    </ul>
                </div>

                <div class="col-lg-4">
                    <h5 class="text-white fw-bold mb-6">Hubungi Kami</h5>
                    <div class="d-flex align-items-start mb-5">
                        <div class="symbol symbol-35px me-4 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                            <i class="material-icons fs-6 text-primary-theme">place</i> 
                        </div>
                        <span class="fs-7 opacity-75 lh-base">{{ $company->alamat_lengkap ?? 'Indonesia' }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-5">
                        <div class="symbol symbol-35px me-4 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                            <i class="material-icons fs-6 text-primary-theme">email</i> 
                        </div>
                        <span class="fs-7 opacity-75">{{ $company->email ?? 'support@findtalen.com' }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-35px me-4 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                            <i class="material-icons fs-6 text-primary-theme">phone</i> 
                        </div>
                        <span class="fs-7 opacity-75">{{ $company->telp ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <hr class="border-white mt-15 mb-8 opacity-10">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-8">
                <p class="mb-4 mb-md-0 fs-8 opacity-50">© {{ date('Y') }} {{ $company->nama_perusahaan ?? 'FindTalen' }}. All Rights Reserved.</p>
                <div class="d-flex gap-6">
                    <a href="#" class="text-decoration-none fs-8 opacity-50 text-white hover-opacity-100 transition-all">Syarat & Ketentuan</a>
                    <a href="#" class="text-decoration-none fs-8 opacity-50 text-white hover-opacity-100 transition-all">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
    </footer>

    <style>
        footer {
            background-color: var(--secondary-color);
            color: rgba(255, 255, 255, 0.7);
        }
        .py-15 { padding-top: 3.75rem; padding-bottom: 3.75rem; }
        .py-20 { padding-top: 5rem; padding-bottom: 5rem; }
        .mt-15 { margin-top: 3.75rem; }
        .mb-10 { margin-bottom: 2.5rem; }
        .hover-elevate-up { transition: transform 0.2s; }
        .hover-elevate-up:hover { transform: translateY(-3px); }
        .transition-link { color: rgba(255, 255, 255, 0.7); transition: all 0.2s; }
        .transition-link:hover { color: #fff; padding-left: 5px; }
        .hover-opacity-100:hover { opacity: 1 !important; }
        @media (max-width: 991.98px) {
            .ps-lg-12 { padding-left: 0 !important; }
            .mb-lg-0 { margin-bottom: 0 !important; }
        }
    </style>

    <!-- Floating WhatsApp Action -->
    <a href="https://wa.me/6285888158640?text=Halo%20Admin%20FindTalen,%20saya%20ingin%20bertanya%20mengenai..." 
       target="_blank" 
       class="wa-float shadow-lg d-flex align-items-center justify-content-center transition-all"
       title="Chat Admin WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.656zm6.29-4.143c1.589.943 3.1 1.411 4.75 1.412 5.513 0 9.996-4.485 9.999-9.998 0-2.67-1.039-5.179-2.924-7.066-1.884-1.886-4.394-2.924-7.064-2.925-5.515 0-10.001 4.487-10.003 10.001 0 1.83.504 3.559 1.46 5.041l-1.08 3.943 4.067-1.068zm11.332-5.41c-.313-.157-1.851-.913-2.138-1.018-.287-.104-.497-.157-.707.157-.21.314-.812 1.018-.994 1.227-.183.21-.365.234-.678.077-.313-.157-1.32-.486-2.515-1.551-.93-.829-1.558-1.854-1.74-2.169-.183-.314-.02-.484.137-.64.141-.141.312-.366.469-.55.157-.183.21-.314.314-.523.104-.21.052-.393-.026-.549-.079-.157-.707-1.701-.969-2.329-.255-.612-.514-.53-.707-.54-.183-.008-.392-.01-.601-.01-.21 0-.549.078-.837.392-.287.314-1.1 1.073-1.1 2.617 0 1.544 1.125 3.036 1.282 3.246.157.21 2.214 3.38 5.362 4.742.748.325 1.332.518 1.787.662.752.24 1.435.205 1.974.125.6-.09 1.851-.758 2.112-1.465.262-.707.262-1.31.183-1.465-.079-.155-.288-.246-.601-.403z"/>
        </svg>
    </a>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/utils.min.js') }}"></script>
    @stack('scripts')
</body>
</html>

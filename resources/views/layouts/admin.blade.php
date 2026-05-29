<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title', 'Dashboard') - {{ $company->nama_perusahaan ?? 'FindTalen' }}</title>
    
    @if($company && $company->favicon)
        <link rel="icon" type="image/png" href="{{ asset($company->favicon) }}">
    @endif
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.MAPBOX_TOKEN = "{{ env('MAPBOX_ACCESS_TOKEN') }}";
    </script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,600,700|Poppins:300,400,500,600,700" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <link href="https://preview.keenthemes.com/metronic8/demo1/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="https://preview.keenthemes.com/metronic8/demo1/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!-- PNotify -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.css" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.brighttheme.css" rel="stylesheet" type="text/css" />
    <!-- NProgress -->
    <link href="https://unpkg.com/nprogress@0.2.0/nprogress.css" rel="stylesheet" type="text/css" />
    <style>
        body { 
            font-family: 'Poppins', 'Roboto', sans-serif !important; 
        }
        h1, h2, h3, h4, h5, h6, .fw-bold, .fw-semibold {
            font-family: 'Poppins', sans-serif !important;
        }
        #nprogress .bar {
            background: #009ef7 !important;
            height: 4px !important;
        }

        /* Sidebar Theme Customization */
        .app-sidebar {
            background-image: linear-gradient(180deg, {{ $company->secondary_color ?? '#450a0a' }}ee 0%, {{ $company->primary_color ?? '#7f1d1d' }}fa 100%), 
                              url('{{ asset('admin/media/sidebar-bg.png') }}') !important;
            background-size: cover !important;
            background-position: center !important;
            border-right: 1px solid rgba(255, 255, 255, 0.05) !important;
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.1);
        }
        
        #kt_app_sidebar_logo {
            background: rgba(255, 255, 255, 0.03) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        .menu-item .menu-link .menu-title {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500 !important;
        }

        .menu-item:hover > .menu-link:not(.disabled):not(.active), 
        .menu-item .menu-link:hover:not(.disabled):not(.active) {
            background-color: rgba(255, 255, 255, 0.08) !important;
        }

        .menu-item .menu-link.active {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%) !important;
            border-left: 4px solid #ffffff !important;
            color: #ffffff !important;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .menu-link .menu-icon i {
            color: rgba(255, 255, 255, 0.45) !important;
            transition: color 0.2s ease;
        }
        
        .menu-item .menu-link.active .menu-icon i,
        .menu-item .menu-link:hover .menu-icon i {
            color: #ffffff !important;
        }

        .menu-heading {
            color: rgba(255, 255, 255, 0.4) !important;
            letter-spacing: 0.05rem;
        }

        .app-sidebar-toggle {
            background-color: #ffffff !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1) !important;
            z-index: 100 !important;
        }
        
        .app-sidebar-toggle i {
            color: {{ $company->primary_color ?? '#7f1d1d' }} !important;
        }

        .app-header {
            background-color: #ffffff !important;
            border-bottom: 3px solid {{ $company->primary_color ?? '#7f1d1d' }} !important;
            box-shadow: 0 1px 15px rgba(0, 0, 0, 0.03) !important;
        }

        .app-navbar-item .btn-icon i {
            color: {{ $company->primary_color ?? '#7f1d1d' }} !important;
            opacity: 0.8;
            transition: all 0.2s ease;
        }

        .app-navbar-item .btn-icon:hover i {
            opacity: 1;
            transform: scale(1.1);
        }
    </style>
    @stack('styles')
</head>
<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <div id="kt_app_header" class="app-header">
                <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
                    <div class="d-flex align-items-center d-lg-none ms-n3 me-2" title="Show sidebar menu">
                        <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                            <i class="ki-duotone ki-abstract-14 fs-2 ps-1"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                        <a href="#" class="d-lg-none">
                            <span class="fw-bold fs-3 text-white">FT</span>
                        </a>
                    </div>
                    <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
                        <div class="app-header-menu app-header-mobile-drawer align-items-stretch"></div>
                        <div class="app-navbar flex-shrink-0">
                            {{-- Notification Bar for Admin Aplikasi --}}
                            @if(Auth::user()->hasRole('Admin Aplikasi') || Auth::user()->hasRole('Superadmin') || Auth::user()->idperusahaan != null)
                                @php
                                    $notificationsQuery = DB::table('system_notifications')
                                        ->where('is_read', 0)
                                        ->orderBy('created_at', 'desc');
                                    
                                    if (Auth::user()->idperusahaan != null) {
                                        $notificationsQuery->where('user_id', Auth::user()->id);
                                    } else {
                                        $notificationsQuery->whereNull('user_id');
                                    }
                                    
                                    $notifications = $notificationsQuery->get();
                                    $notifCount = $notifications->count();
                                @endphp
                                <div class="app-navbar-item ms-1 ms-md-3">
                                    <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px position-relative" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                        <i class="material-icons fs-2">notifications</i>
                                        @if($notifCount > 0)
                                            <span class="bullet bullet-dot bg-danger h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"></span>
                                        @endif
                                    </div>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
                                        <div class="d-flex flex-column bgi-no-repeat rounded-top bg-primary pb-5 pt-5 px-5">
                                            <h3 class="text-white fw-semibold mb-0">Notifikasi Sistem</h3>
                                            <span class="badge badge-light-danger fs-8 fw-bold mt-2">{{ $notifCount }} Belum Dibaca</span>
                                        </div>
                                        <div class="scroll-y mh-325px my-5 px-5">
                                            @forelse($notifications as $notif)
                                                <div class="d-flex flex-stack py-4 border-bottom border-gray-200">
                                                    <div class="d-flex align-items-center me-2">
                                                        <div class="symbol symbol-35px me-4">
                                                            <span class="symbol-label bg-light-primary">
                                                                <i class="material-icons text-primary fs-5">business</i>
                                                            </span>
                                                        </div>
                                                        <div class="mb-1">
                                                            <a href="{{ route('admin.notifications.read', $notif->id) }}" class="text-gray-800 text-hover-primary fw-bold fs-7">{{ $notif->title }}</a>
                                                            <div class="text-gray-400 fw-semibold fs-8">{{ $notif->message }}</div>
                                                            <div class="text-gray-400 fs-9">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-10 opacity-50">
                                                    <i class="material-icons fs-2hx mb-2">notifications_none</i>
                                                    <div class="fw-semibold">Tidak ada notifikasi baru</div>
                                                </div>
                                            @endforelse
                                        </div>
                                        <div class="py-3 text-center border-top">
                                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-color-gray-600 btn-active-color-primary fw-bold fs-8">Lihat Semua</a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="app-navbar-item ms-1 ms-md-3" id="kt_header_user_menu_toggle">
                                @php
                                    $user = Auth::user();
                                    $profilePhoto = ($user->gambar && $user->gambar !== 'no-image') 
                                        ? asset('storage/' . $user->gambar) 
                                        : 'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/300-1.jpg';
                                @endphp
                                <div class="cursor-pointer symbol symbol-35px symbol-md-45px symbol-circle border border-gray-300 border-2 shadow-sm" 
                                     data-kt-menu-trigger="{default: 'click', lg: 'hover'}" 
                                     data-kt-menu-attach="parent" 
                                     data-kt-menu-placement="bottom-end">
                                    <img src="{{ $profilePhoto }}" alt="user" />
                                </div>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-300px shadow-lg border-0" data-kt-menu="true">
                                    <div class="menu-item px-3 mb-2">
                                        <div class="menu-content d-flex align-items-center px-3 py-3 rounded-3 bg-light">
                                            <div class="symbol symbol-50px symbol-circle me-5">
                                                <img alt="User Photo" src="{{ $profilePhoto }}" />
                                            </div>
                                            <div class="d-flex flex-column">
                                                <div class="fw-bold d-flex align-items-center fs-5 text-gray-900">
                                                    {{ Str::limit($user->name, 15) }}
                                                    <span class="badge badge-light-danger fw-bold fs-9 px-2 py-1 ms-2 rounded-pill">Admin</span>
                                                </div>
                                                <a href="mailto:{{ $user->email }}" class="fw-semibold text-muted text-hover-primary fs-8 text-break">{{ $user->email }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="separator my-2 opacity-100"></div>
                                    <div class="menu-item px-5 py-1">
                                        <a href="{{ route('admin.profile.index') }}" class="menu-link px-5 rounded-3">
                                            <i class="material-icons text-primary fs-3 me-4">person</i>
                                            <span class="fw-bold">Profil Saya</span>
                                        </a>
                                    </div>
                                    <div class="menu-item px-5 py-1">
                                        <a href="{{ Auth::user()->hasRole('Admin Perusahaan') ? route('admin.perusahaan.dashboard') : route('admin.dashboard') }}" class="menu-link px-5 rounded-3">
                                            <i class="material-icons text-success fs-3 me-4">dashboard</i>
                                            <span class="fw-bold">Dashboard Saya</span>
                                        </a>
                                    </div>
                                    <div class="separator my-2 opacity-50"></div>
                                    <div class="menu-item px-5 py-1">
                                        <a href="{{ url('logout') }}" class="menu-link px-5 rounded-3 text-hover-danger">
                                            <i class="material-icons text-danger fs-3 me-4">logout</i>
                                            <span class="fw-bold">Keluar</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
                    <div class="app-sidebar-logo px-6 d-flex align-items-center" id="kt_app_sidebar_logo">
                        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                            @if($company && $company->logo)
                                <img src="{{ asset($company->logo) }}" class="h-40px me-3" alt="Logo">
                            @endif
                            <span class="fw-bold fs-4 text-white lh-sm">{{ $company->nama_perusahaan ?? 'FindTalen' }} <span class="fs-9 opacity-50 d-block">Admin Portal</span></span>
                        </a>
                        <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
                            <i class="material-icons fs-5 rotate-180">first_page</i>
                        </div>
                    </div>
                    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
                        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
                            <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.perusahaan.dashboard') ? 'active' : '' }}" 
                                       href="{{ Auth::user()->hasRole('Admin Perusahaan') ? route('admin.perusahaan.dashboard') : route('admin.dashboard') }}">
                                        <span class="menu-icon">
                                            <i class="material-icons fs-2">dashboard</i>
                                        </span>
                                        <span class="menu-title">Dashboard</span>
                                    </a>
                                </div>
                                <div class="menu-item pt-5">
                                    <div class="menu-content">
                                        <span class="menu-heading fw-bold text-uppercase fs-7">Menu Utama</span>
                                    </div>
                                </div>
                                @php
                                    $user = Auth::user();
                                    $roleIds = $user->roles->pluck('id')->toArray();
                                    $isAdminPerusahaan = $user->hasRole('Admin Perusahaan');
                                    $isValidated = $user->statusvalidasi == 1;
                                    
                                    // Ambil menu level 0 (parent utama)
                                    $menus = [];
                                    if (!$isAdminPerusahaan || $isValidated) {
                                        $menus = \App\Models\Menu::where(function($q) {
                                                $q->whereNull('idmenu')->orWhere('idmenu', 0);
                                            })
                                            ->whereHas('aksesmenus', function($query) use ($roleIds) {
                                                $query->whereIn('idrole', $roleIds);
                                            })
                                            ->orderBy('id', 'asc')
                                            ->get();
                                    }
                                @endphp

                                @if($isAdminPerusahaan && !$isValidated)
                                    {{-- Menu wajib untuk yang belum divalidasi --}}
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('admin.perusahaan.profile') ? 'active' : '' }}" href="{{ route('admin.perusahaan.profile') }}">
                                            <span class="menu-icon">
                                                <i class="material-icons fs-2">person_outline</i>
                                            </span>
                                            <span class="menu-title text-warning fw-bold">Lengkapi Profil</span>
                                        </a>
                                    </div>
                                @endif

                                @foreach($menus as $menu)
                                    @php
                                        // Cari submenus untuk menu ini yang user punya akses
                                        $submenus = \App\Models\Menu::where('idmenu', $menu->id)
                                            ->whereHas('aksesmenus', function($query) use ($roleIds) {
                                                $query->whereIn('idrole', $roleIds);
                                            })
                                            ->get();
                                    @endphp

                                    @if($menu->submenu == 1 || $submenus->count() > 0)
                                        {{-- Menu dengan Submenu (Accordion) --}}
                                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                                            <span class="menu-link {{ request()->is(trim($menu->alamat_url, '/').'*') ? 'active' : '' }}">
                                                <span class="menu-icon">
                                                    <i class="material-icons fs-2">{{ $menu->icon ?? 'apps' }}</i>
                                                </span>
                                                <span class="menu-title">{{ $menu->namamenu }}</span>
                                                <span class="menu-arrow"></span>
                                            </span>
                                            <div class="menu-sub menu-sub-accordion">
                                                @foreach($submenus as $sub)
                                                    <div class="menu-item">
                                                        <a class="menu-link {{ request()->is(trim($sub->alamat_url, '/').'*') ? 'active' : '' }}" href="{{ $sub->alamat_url }}">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">{{ $sub->namamenu }}</span>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        {{-- Menu Tunggal --}}
                                        <div class="menu-item">
                                            <a class="menu-link {{ request()->is(trim($menu->alamat_url, '/').'*') ? 'active' : '' }}" href="{{ $menu->alamat_url }}">
                                                <span class="menu-icon">
                                                    <i class="material-icons fs-2">{{ $menu->icon ?? 'apps' }}</i>
                                                </span>
                                                <span class="menu-title">{{ $menu->namamenu }}</span>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">@yield('page_title', 'Dashboard')</h1>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">Halaman</li>
                                        <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                                        <li class="breadcrumb-item text-muted">@yield('page_title', 'Dashboard')</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container container-fluid">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                    <div id="kt_app_footer" class="app-footer">
                        <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
                            <div class="text-dark order-2 order-md-1">
                                <span class="text-muted fw-semibold me-1">{{ date('Y') }}&copy;</span>
                                <a href="#" class="text-gray-800 text-hover-primary fw-bold">{{ $company->nama_perusahaan ?? 'FindTalen Team' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://preview.keenthemes.com/metronic8/demo1/assets/plugins/global/plugins.bundle.js"></script>
    <script src="https://preview.keenthemes.com/metronic8/demo1/assets/js/scripts.bundle.js"></script>
    <!-- PNotify -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.buttons.js"></script>
    <!-- NProgress -->
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
    
    <script src="{{ asset('js/utils.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            @if(session('success')) showNotification('Sukses', "{{ session('success') }}", 'success'); @endif
            @if($errors->any()) @foreach($errors->all() as $error) showNotification('Perhatian', "{{ $error }}", 'warning'); @endforeach @endif
            @if(session('error')) showNotification('Gagal', "{{ session('error') }}", 'error'); @endif
        });
    </script>

    @stack('scripts')
</body>
</html>

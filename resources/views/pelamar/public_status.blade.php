<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Lamaran - {{ $pelamar->namalengkap }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .header { background: #fff; padding: 25px 0; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-bottom: 2px solid #7f1d1d; }
        .profile-info { text-align: center; margin-top: -50px; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.1); object-fit: cover; background: #fff; }
        .job-card { background: #fff; border-radius: 20px; border: 1px solid rgba(0,0,0,0.05); transition: all 0.3s; margin-bottom: 15px; overflow: hidden; }
        .job-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .company-logo { width: 45px; height: 45px; border-radius: 12px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .badge-status { font-size: 0.7rem; font-weight: 800; padding: 6px 12px; border-radius: 50px; text-transform: uppercase; }
        .btn-theme { background: #7f1d1d; color: #fff; border: none; font-weight: 700; border-radius: 50px; padding: 12px 25px; }
        .btn-theme:hover { background: #450a0a; color: #fff; }
    </style>
</head>
<body>

    <div class="header mb-5">
        <div class="container text-center">
            <h5 class="fw-bold text-dark mb-0">Verifikasi Data Lamaran</h5>
            <p class="text-muted small mb-0">{{ $event->namaperiode }}</p>
        </div>
    </div>

    <div class="container pb-5">
        <div class="profile-info mb-5">
            @if($pelamar->foto)
                <img src="{{ asset('storage/'.$pelamar->foto) }}" class="profile-img shadow-lg">
            @else
                <div class="profile-img d-inline-flex align-items-center justify-content-center bg-secondary text-white">
                    <i class="material-icons fs-1 text-white">person</i>
                </div>
            @endif
            <h4 class="fw-bold mt-3 text-dark mb-1">{{ $pelamar->namalengkap }}</h4>
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <span class="badge bg-light text-muted fw-bold border border-secondary border-opacity-10 rounded-pill px-3 py-2">ID: #{{ str_pad($pelamar->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span class="badge bg-light text-primary-theme fw-bold border border-primary-theme border-opacity-10 rounded-pill px-3 py-2"><i class="material-icons fs-9 align-middle me-1">phone</i> {{ $pelamar->nohp }}</span>
            </div>
            <p class="text-muted small px-4">Berikut adalah rincian lowongan yang telah dilamar oleh peserta di event ini.</p>
        </div>

        <div class="row">
            @forelse($lamarans as $lamaran)
            <div class="col-12">
                <div class="job-card p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="company-logo">
                            @if($lamaran->lowongan->register->perusahaan->logo)
                                <img src="{{ asset('storage/'.$lamaran->lowongan->register->perusahaan->logo) }}" style="width: 32px; height: 32px; object-fit: contain;">
                            @else
                                <i class="material-icons text-muted">business</i>
                            @endif
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="fw-bold text-dark fs-7 mb-1 text-truncate">{{ $lamaran->lowongan->namalowongan }}</h6>
                            <span class="text-muted fs-8 d-block text-truncate">{{ $lamaran->lowongan->register->perusahaan->nama }}</span>
                        </div>
                    </div>
                    
                    <hr class="my-3 opacity-10">
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-column">
                            <span class="fs-9 text-muted text-uppercase fw-bold ls-1 mb-1">Jadwal Sesi</span>
                            <div class="d-flex align-items-center text-dark fw-bold fs-7">
                                <i class="material-icons text-primary-theme fs-8 me-2">schedule</i>
                                {{ $lamaran->sesi->nama_sesi ?? 'Umum/Tanpa Sesi' }}
                                @if($lamaran->sesi)
                                    <span class="text-muted fs-8 ms-2 fw-normal">({{ substr($lamaran->sesi->jam_mulai, 0, 5) }} - {{ substr($lamaran->sesi->jam_selesai, 0, 5) }})</span>
                                @endif
                            </div>
                        </div>
                        @php
                            $isHadir = $lamaran->kehadirans()->exists();
                        @endphp
                        <span class="badge {{ $isHadir ? 'bg-success' : 'bg-warning text-dark' }} badge-status px-3 py-2">
                           <i class="material-icons fs-9 align-middle me-1">{{ $isHadir ? 'check_circle' : 'pending' }}</i>
                           {{ $isHadir ? 'Hadir' : 'Belum Absen' }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center p-5 bg-white rounded-4 border border-dashed text-muted">
                    <i class="material-icons fs-1 mb-3 opacity-25">find_in_page</i>
                    <p class="small mb-0">Belum ada lowongan yang dilamar di event ini.</p>
                </div>
            </div>
            @endforelse
        </div>

        <div class="text-center mt-5">
            <p class="fs-10 text-muted">&copy; 2024 FindTalen Recruitment Portal &bull; Keamanan Terjamin</p>
        </div>
    </div>

</body>
</html>

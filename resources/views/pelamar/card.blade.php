<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Peserta - {{ $pelamar->namalengkap }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-container { width: 100%; max-width: 450px; margin: 40px auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); position: relative; border: 1px solid #eee; }
        .card-header-custom { background: linear-gradient(135deg, #7f1d1d 0%, #450a0a 100%); padding: 30px; color: white; text-align: center; }
        .profile-photo { width: 120px; height: 120px; border-radius: 50%; border: 5px solid white; object-fit: cover; margin-top: -60px; background: white; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .qr-section { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 15px; margin: 20px; border: 1px dashed #ddd; }
        .info-table { width: 100%; font-size: 0.85rem; }
        .info-table td { padding: 8px 0; border-bottom: 1px solid #f1f1f1; }
        .info-label { color: #666; font-weight: 600; width: 120px; }
        .info-value { color: #111; font-weight: 700; }
        .badge-event { background: rgba(127, 29, 29, 0.1); color: #7f1d1d; font-weight: 800; font-size: 0.7rem; text-transform: uppercase; padding: 5px 12px; border-radius: 50px; display: inline-block; margin-bottom: 5px; }
        
        @media print {
            body { background: white; padding: 0; margin: 0; }
            .card-container { margin: 0; box-shadow: none; border: 1px solid #ccc; width: 100%; max-width: 100%; height: auto; border-radius: 0; }
            .no-print { display: none; }
            .card-header-custom { -webkit-print-color-adjust: exact; }
        }
        
        .btn-print { position: fixed; bottom: 30px; right: 30px; z-index: 1000; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn btn-danger btn-lg rounded-pill px-5 fw-bold btn-print no-print">
        <i class="material-icons align-middle me-2">print</i> Cetak Kartu
    </button>

    <div class="card-container">
        <div class="card-header-custom">
            <h5 class="mb-1 text-uppercase ls-2">Kartu Peserta</h5>
            <div class="badge-event" style="background: white;">Bursa Kerja Internal</div>
            <h4 class="fw-bold mb-0 mt-2">{{ $event->namaperiode }}</h4>
        </div>

        <div class="text-center">
            @if($pelamar->foto)
                <img src="{{ asset('storage/'.$pelamar->foto) }}" class="profile-photo" alt="Foto Profil">
            @else
                <div class="profile-photo d-inline-flex align-items-center justify-content-center bg-secondary text-white">
                    <i class="material-icons fs-1 text-white">person</i>
                </div>
            @endif
        </div>

        <div class="p-4">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark mb-0">{{ $pelamar->namalengkap }}</h3>
                <span class="text-muted small">ID Pelamar: #{{ str_pad($pelamar->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>

            <table class="info-table">
                <tr>
                    <td class="info-label">No. KTP</td>
                    <td class="info-value">{{ $pelamar->noktp }}</td>
                </tr>
                <tr>
                    <td class="info-label">Pendidikan</td>
                    <td class="info-value">
                        @php $latestEdu = $pelamar->pendidikans()->latest()->first(); @endphp
                        {{ $latestEdu ? $latestEdu->nama_sekolah : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="info-label">No. WhatsApp</td>
                    <td class="info-value">{{ $pelamar->nohp }}</td>
                </tr>
                <tr>
                    <td class="info-label">Domisili</td>
                    <td class="info-value">{{ $pelamar->kelurahan->kecamatan->kota->nama ?? 'Indonesia' }}</td>
                </tr>
            </table>

            <div class="qr-section mt-4">
                <p class="fs-9 text-muted mb-2 text-uppercase fw-bold">Scan untuk Verifikasi Lamaran</p>
                @php
                    $scannedUrl = route('public.applicant.status', [
                        'encrypted_id' => encrypt($pelamar->id),
                        'ideven' => encrypt($event->id)
                    ]);
                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($scannedUrl);
                @endphp
                <img src="{{ $qrUrl }}" alt="QR Code" class="img-fluid border p-2 bg-white rounded shadow-sm" style="max-width: 140px;">
                <p class="fs-10 text-muted mt-2 mt-2">Kartu ini valid untuk event <strong>{{ $event->namaperiode }}</strong></p>
            </div>
        </div>

        <div class="bg-light p-3 text-center border-top">
            <p class="mb-0 fs-10 text-muted">Dihasilkan secara otomatis oleh sistem FindTalen &bull; {{ date('d/m/Y H:i') }}</p>
        </div>
    </div>

</body>
</html>

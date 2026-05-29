<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to FindTalen</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { max-width: 150px; height: auto; }
        .welcome-title { color: #2c3e50; font-size: 24px; font-weight: 700; margin-bottom: 20px; }
        .content { color: #555; margin-bottom: 30px; }
        .credentials { background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .credential-item { margin-bottom: 10px; }
        .credential-label { color: #64748b; font-size: 14px; margin-bottom: 5px; }
        .credential-value { color: #1e293b; font-family: monospace; font-size: 18px; font-weight: 600; }
        .warning-box { border-left: 4px solid #ef4444; background-color: #fef2f2; padding: 15px; margin-bottom: 30px; color: #991b1b; font-size: 14px; }
        .cta-container { text-align: center; }
        .btn { display: inline-block; padding: 12px 30px; background-color: #1a56db; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08); transition: all 0.2s; }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="welcome-title">Selamat Datang di FindTalen</h1>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $company->nama }}</strong>,</p>
            <p>Perusahaan Anda telah berhasil didaftarkan oleh Administrator Sistem di platform FindTalen. Sekarang Anda dapat mulai mengelola lowongan kerja dan menemukan talenta-talenta terbaik untuk tim Anda.</p>
            
            <p>Berikut adalah rincian akun login Anda:</p>
        </div>

        <div class="credentials">
            <div class="credential-item">
                <div class="credential-label">Username (Email)</div>
                <div class="credential-value">{{ $user->email }}</div>
            </div>
            <div class="credential-item">
                <div class="credential-label">Password Sementara</div>
                <div class="credential-value">{{ $password }}</div>
            </div>
        </div>

        <div class="warning-box">
            <strong>⚠️ PENTING:</strong> Segera ganti password sementara Anda demi keamanan akun saat pertama kali login.
        </div>

        <div class="content">
            <p>Ayo mulai langkah pertama Anda dengan melengkapi profil perusahaan dan **menambahkan lowongan kerja** terbaru.</p>
        </div>

        <div class="cta-container">
            <a href="{{ url('/login') }}" class="btn">Login ke Dashboard</a>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} FindTalen Rekrutmen Portal. Semua Hak Dilindungi.<br>
            Jl. Raya Perusahaan No. 123, Jakarta Selatan, Indonesia.
        </div>
    </div>
</body>
</html>

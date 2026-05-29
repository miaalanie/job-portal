<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #7f1d1d; margin-bottom: 5px; }
        .header p { color: #666; margin-top: 0; }
        .content { margin-bottom: 30px; }
        .footer { text-align: center; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
        .button { display: inline-block; padding: 14px 30px; background-color: #7f1d1d; color: #ffffff; text-decoration: none; border-radius: 30px; font-weight: bold; }
        .info-card { background: #fff8f8; padding: 20px; border-radius: 12px; border: 1px solid #ffeded; margin-bottom: 25px; }
        .price-badge { background: #7f1d1d; color: #fff; padding: 8px 15px; border-radius: 5px; font-weight: bold; font-size: 18px; }
        .steps { padding-left: 20px; color: #555; }
        .steps li { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="font-weight: 800; letter-spacing: -1px;">FindTalen Recruitment</h1>
            <p>Pendaftaran Event Berhasil</p>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $perusahaan->nama }}</strong>,</p>
            <p>Terima kasih telah mendaftar untuk berpartisipasi dalam event <strong>{{ $even->namaperiode }}</strong>. Kami sangat antusias menyambut Anda!</p>
            
            <div class="info-card">
                <h3 style="margin-top: 0; color: #111;">Ringkasan Pendaftaran:</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 5px 0; color: #666;">Paket / Opsi:</td>
                        <td style="padding: 5px 0; font-weight: bold;">{{ $register->namapaket }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #666;">Biaya Investasi:</td>
                        <td style="padding: 10px 0;">
                            <span class="price-badge">IDR {{ number_format($register->biaya, 0, ',', '.') }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            <h4 style="color: #7f1d1d; border-bottom: 2px solid #ffeded; padding-bottom: 5px;">Langkah Selanjutnya:</h4>
            <ul class="steps">
                <li><strong>Instruksi Pembayaran:</strong> Silakan lakukan pembayaran sesuai dengan biaya di atas ke nomor rekening operasional kami yang tertera di menu "Invoices" pada dashboard.</li>
                <li><strong>Konfirmasi Admin:</strong> Setelah pembayaran diterima, admin kami akan melakukan validasi keikutsertaan Anda (Status Aktivasi: Aktif).</li>
                <li><strong>Posting Lowongan:</strong> Setelah aktif, Anda dapat langsung mulai memposting lowongan kerja yang akan ditampilkan di halaman depan event.</li>
            </ul>

            <div style="text-align: center; margin-top: 40px;">
                <a href="{{ url('/admin/dashboard') }}" class="button" style="color: #ffffff !important;">Ke Dashboard Perusahaan</a>
            </div>
            
            <p style="margin-top: 30px; font-size: 14px; color: #666; font-style: italic;">
                *Pendaftaran akan dibatalkan otomatis jika tidak ada konfirmasi pembayaran dalam 3x24 jam.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} FindTalen Platform by Antigravity. All rights reserved.</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Update Status Validasi Perusahaan</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px;">
        <h2 style="color: #d32f2f;">Pemberitahuan Status Validasi: {{ $user->perusahaan->nama }}</h2>
        <p>Terima kasih telah mendaftar di <strong>FindTalen</strong>. Kami telah meninjau profil dan dokumen yang Anda kirimkan.</p>
        
        <p>Sayangnya, saat ini kami belum dapat memvalidasi akun perusahaan Anda karena alasan berikut:</p>
        
        <div style="background-color: #ffebee; border-left: 5px solid #d32f2f; padding: 15px; margin: 20px 0;">
            <strong>Alasan/Saran Peninjauan:</strong><br>
            {{ $reason }}
        </div>

        <p>Anda tetap dapat masuk ke dashboard untuk **melengkapi data atau memperbaiki dokumen** yang diperlukan melalui menu "Lengkapi Profil".</p>

        <div style="margin: 30px 0; text-align: center;">
            <a href="{{ route('login') }}" style="background-color: #d32f2f; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Kunjungi Portal Pendaftaran</a>
        </div>

        <p>Silakan hubungi tim kami jika Anda memerlukan bantuan lebih lanjut.</p>
        <p>Salam,<br>Tim FindTalen</p>
    </div>
</body>
</html>

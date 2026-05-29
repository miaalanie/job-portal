<!DOCTYPE html>
<html>
<head>
    <title>Akun Perusahaan Terverifikasi</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px;">
        <h2 style="color: #00796B;">Selamat, {{ $user->perusahaan->nama }}!</h2>
        <p>Kami telah meninjau profil dan dokumen legalitas perusahaan Anda. Berdasarkan hasil tinjauan kami, <strong>Akun Anda telah divalidasi dan kini aktif sepenuhnya.</strong></p>
        
        <p>Anda sekarang dapat mengakses dashboard admin untuk:</p>
        <ul>
            <li>Memposting lowongan kerja baru</li>
            <li>Mengelola pendaftaran event/job fair</li>
            <li>Melihat daftar pelamar</li>
        </ul>

        <div style="margin: 30px 0; text-align: center;">
            <a href="{{ route('login') }}" style="background-color: #00796B; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Masuk ke Dashboard</a>
        </div>

        <p>Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi tim support kami.</p>
        <p>Terima kasih,<br>Tim FindTalen</p>
    </div>
</body>
</html>

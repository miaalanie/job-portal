<!DOCTYPE html>
<html>
<head>
    <title>Aktivasi Akun</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; border-top: 5px solid #e11d48;">
        <h2 style="color: #e11d48;">Selamat Datang di FindTalen!</h2>
        <p>Halo <strong>{{ $companyName }}</strong>,</p>
        <p>Terima kasih telah mendaftarkan akun di platform FindTalen. Berikut adalah detail akun Anda:</p>
        <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 5px 0;"><strong>Identitas:</strong> {{ $companyName }}</p>
            <p style="margin: 5px 0;"><strong>Email Login:</strong> {{ $email }}</p>
            <p style="margin: 5px 0;"><strong>Password:</strong> {{ $password }}</p>
        </div>
        <p>Untuk mengaktifkan akun Anda, silakan klik tombol di bawah ini:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('perusahaan.activate', ['token' => $user->activation_token]) }}" 
               style="background-color: #e11d48; color: white; padding: 15px 30px; text-decoration: none; border-radius: 50px; font-weight: bold; display: inline-block;">
               Aktifkan Akun Sekarang
            </a>
        </div>
        <p>Setelah akun aktif, Anda dapat login dan melengkapi dokumen legalitas perusahaan untuk verifikasi oleh tim admin kami.</p>
        <p>Jika tombol di atas tidak berfungsi, silakan salin dan tempel tautan berikut ke browser Anda:</p>
        <p>{{ route('perusahaan.activate', ['token' => $user->activation_token]) }}</p>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">
        <p style="color: #777; font-size: 12px;">Pesan ini dikirim secara otomatis oleh sistem FindTalen. Jangan membalas email ini.</p>
    </div>
</body>
</html>

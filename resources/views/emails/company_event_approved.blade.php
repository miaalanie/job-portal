<x-mail::message>
# Pendaftaran Event Disetujui! 🚀

Halo **{{ $register->perusahaan->nama }}**,

Kami dengan senang hati menginformasikan bahwa pendaftaran dan pembayaran Anda untuk event **{{ $register->even->namaperiode }}** telah berhasil **DIVERIFIKASI**.

### Detail Keikutsertaan:
* **Event:** {{ $register->even->namaperiode }}
* **Paket:** {{ $register->namapaket }}
* **Status:** Aktif (Siap Berpartisipasi)

**Sekarang Anda sudah dapat mulai mengunggah lowongan pekerjaan** yang akan ditampilkan secara khusus pada bursa kerja event tersebut melalui dashboard mitra Anda.

<x-mail::button :url="url('/admin/dashboard')">
Masuk & Posting Lowongan
</x-mail::button>

Terima kasih telah menjadi bagian dari ekosistem rekrutmen kami. Sampai jumpa di lokasi event!

Salam Hangat,<br>
**Tim FindTalen Recruitment**
</x-mail::message>

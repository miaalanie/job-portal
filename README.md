# Nusa Raya Career Day - Job Portal Application

Aplikasi job portal untuk mengelola job fair "Nusa Raya Career Day". Platform ini memudahkan pencari kerja untuk menemukan lowongan yang sesuai dan menghadiri event walk-in interview, serta membantu perusahaan dalam merekrut talenta terbaik.

## 📱 Akses Aplikasi

**Link Aplikasi:** [https://nusarayacareerday.com/](https://nusarayacareerday.com/)

## 🎯 Tentang Aplikasi

Nusa Raya Career Day Job Portal adalah platform terintegrasi yang menghubungkan pencari kerja dengan perusahaan di event job fair. Aplikasi ini dirancang untuk memberikan pengalaman terbaik bagi kedua belah pihak dengan fitur rekomendasi cerdas dan manajemen event yang efisien.

### Fitur Utama:

- **Registrasi Pencari Kerja**: Pengguna dapat mendaftar dan membuat profil profesional mereka
- **Pencarian & Rekomendasi Lowongan**: Sistem rekomendasi berbasis AI untuk mencocokkan kandidat dengan lowongan yang relevan
- **Event Management**: Browsing dan pendaftaran ke berbagai job fair events
- **Walk-in Interview**: Sistem pendaftaran untuk walk-in interview langsung di venue job fair
- **Application Tracking**: Tracking status lamaran untuk pencari kerja
- **Ranking Lamaran**: Sistem ranking lamaran otomatis untuk perusahaan agar mudah memilih kandidat terbaik

---

## 👥 Alur Pengguna (User Flow)

### Untuk Pencari Kerja:

1. **Registrasi & Login**
   - Calon pekerja mendaftar akun di aplikasi
   - Melengkapi profil dengan data pribadi, keahlian, dan pengalaman kerja

2. **Jelajahi Event**
   - Melihat daftar job fair dan event yang tersedia
   - Membaca detail event termasuk waktu, lokasi, dan perusahaan yang berpartisipasi

3. **Pilih Event & Daftar**
   - Memilih event yang ingin dihadiri
   - Melakukan registrasi untuk mengikuti event tersebut

4. **Browse Lowongan**
   - Melihat lowongan dari perusahaan di event yang terpilih
   - Mendapatkan rekomendasi lowongan yang sesuai dengan profil mereka

5. **Walk-in Interview**
   - Datang ke venue job fair pada waktu yang dijadwalkan
   - Bertemu langsung dengan recruiter perusahaan
   - Melakukan interview di booth perusahaan

6. **Track Aplikasi**
   - Memantau status lamaran yang telah dikirim
   - Menerima notifikasi update dari perusahaan

---

## 🤖 ML Service - Content-Based Filtering

### Sistem Rekomendasi Lowongan

Aplikasi menggunakan teknologi **Content-Based Filtering** untuk memberikan rekomendasi lowongan yang paling sesuai dengan profil pencari kerja:

**Cara Kerja:**
- **Analisis Profil Kandidat**: Sistem menganalisis skills, pengalaman, education, dan preferensi kerja dari profil pengguna
- **Matching dengan Lowongan**: Setiap lowongan di-parse untuk ekstrak requirements, skills yang dibutuhkan, dan karakteristik lainnya
- **Scoring & Ranking**: Sistem memberikan skor kecocokan (similarity score) berdasarkan perbandingan konten profil dengan requirement lowongan
- **Personalisasi**: Rekomendasi ditampilkan berdasarkan skor tertinggi, memastikan pencari kerja melihat lowongan yang paling relevan terlebih dahulu

**Benefit:**
- Menghemat waktu pencari kerja dalam mencari lowongan yang sesuai
- Meningkatkan akurasi matching antara kandidat dan posisi yang tersedia
- Memberikan pengalaman yang lebih personal dan relevan

### Sistem Ranking Lamaran (Untuk Perusahaan)

Aplikasi menyediakan fitur ranking lamaran otomatis untuk membantu perusahaan dalam mengidentifikasi kandidat terbaik:

**Cara Kerja:**
- **Analisis Lamaran**: Sistem menganalisis semua lamaran yang masuk untuk posisi tertentu
- **Scoring Kandidat**: Setiap kandidat di-score berdasarkan:
  - Kecocokan skills dengan job requirements
  - Pengalaman yang relevan dengan posisi
  - Level pendidikan yang dibutuhkan
  - Faktor-faktor lainnya yang spesifik untuk pekerjaan
- **Automatic Ranking**: Lamaran di-rank secara otomatis dari kandidat terbaik hingga kurang sesuai
- **Filtering & Shortlisting**: Perusahaan dapat dengan mudah melihat top candidates dan melakukan shortlisting

**Benefit:**
- Menghemat waktu HR dalam mengevaluasi ratusan lamaran
- Meningkatkan kualitas kandidat yang dipilih untuk interview
- Memastikan tidak ada kandidat potensial yang terlewat
- Mempercepat proses hiring secara keseluruhan

---

## 🏗️ Struktur Teknis

**Technology Stack:**
- **Backend**: Laravel (PHP)
- **Frontend**: Vue.js / Inertia.js
- **Database**: MySQL
- **ML/Recommendation Engine**: Python (Content-Based Filtering)
- **Styling**: Tailwind CSS

**Key Features:**
- RESTful API
- Real-time Event Updates
- User Authentication & Authorization
- Database Migrations & Seeding
- Multi-role Support (Admin, Company, Job Seeker)

---

## 🚀 Keunggulan Platform

✅ **User-Friendly**: Interface yang intuitif dan mudah digunakan  
✅ **Smart Matching**: Teknologi AI untuk rekomendasi yang akurat  
✅ **Efisien**: Menghemat waktu recruiter dan pencari kerja  
✅ **Terukur**: Data-driven insights untuk recruitment decision  
✅ **Scalable**: Dapat menangani ribuan kandidat dan lowongan  

---

Untuk informasi lebih lanjut tentang event atau pertanyaan lainnya, silakan kunjungi website kami di [https://nusarayacareerday.com/](https://nusarayacareerday.com/)

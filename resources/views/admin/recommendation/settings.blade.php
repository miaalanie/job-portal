@extends('layouts.admin')

@section('title', 'Pengaturan Rekomendasi')
@section('page_title', 'Pengaturan Sistem Rekomendasi')

@section('content')
<div class="row g-6">
    <div class="col-xl-7">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title">Bobot Penilaian</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('admin.recommendation.settings.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-5">
                        @foreach([
                            'weight_semantic' => ['Kesesuaian profil', 'Seberapa mirip profil dan deskripsi lowongan.'],
                            'weight_skill' => ['Kecocokan skill', 'Seberapa banyak skill pelamar memenuhi kebutuhan lowongan.'],
                            'weight_education' => ['Pendidikan', 'Kesesuaian jenjang dan jurusan pendidikan.'],
                            'weight_experience' => ['Pengalaman', 'Kesesuaian posisi dan lama pengalaman kerja.'],
                        ] as $field => [$label, $help])
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $label }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="{{ $field }}" min="0" max="1" step="0.01" value="{{ old($field, $settings->{$field}) }}" required>
                                    <span class="input-group-text">= {{ number_format(old($field, $settings->{$field}) * 100, 0) }}%</span>
                                </div>
                                <div class="form-text">{{ $help }} Masukkan angka 0 sampai 1.</div>
                            </div>
                        @endforeach
                        <div class="col-12">
                            <label class="form-label fw-bold">Ambang kecocokan skill</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="skill_threshold" min="0" max="1" step="0.01" value="{{ old('skill_threshold', $settings->skill_threshold) }}" required>
                                <span class="input-group-text">{{ number_format(old('skill_threshold', $settings->skill_threshold) * 100, 0) }}%</span>
                            </div>
                            <div class="form-text">Minimal kemiripan sebuah skill agar dihitung cocok. Nilai lebih tinggi berarti sistem lebih ketat.</div>
                        </div>
                    </div>
                    <div class="alert alert-light-primary mt-6 mb-0">
                        <strong>Catatan:</strong> total empat bobot harus tepat 1.00 atau 100%. Perubahan berlaku pada proses rekomendasi dan ranking berikutnya.
                    </div>
                    <button class="btn btn-primary mt-6" type="submit"><i class="material-icons fs-5 me-1">save</i>Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card shadow-sm h-100">
            <div class="card-header"><h3 class="card-title">Cara Membaca Pengaturan</h3></div>
            <div class="card-body text-gray-700">
                <p>Bobot adalah tingkat kepentingan setiap sumber informasi dalam nilai akhir rekomendasi. Misalnya, bobot skill 0.40 membuat skill menyumbang 40% dari nilai akhir.</p>
                <p>Ambang skill bukan bobot. Ini adalah batas kemiripan antara skill pelamar dan skill lowongan. Jika terlalu rendah, kecocokan yang kurang relevan bisa ikut dihitung.</p>
                <p class="mb-0">Gunakan halaman <strong>Evaluasi Sistem</strong> untuk melihat gambaran data lamaran dan halaman <strong>Health ML Service</strong> untuk memastikan mesin rekomendasi siap digunakan.</p>
            </div>
        </div>
    </div>
</div>
@endsection

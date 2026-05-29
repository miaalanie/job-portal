@extends('layouts.frontend')

@section('title', 'Lengkapi Profil Pelamar - FindTalen')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.brighttheme.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .is-invalid-custom { border: 2px solid #dc3545 !important; }
    .fs-9 { font-size: 0.75rem; }
    .fs-10 { font-size: 0.65rem; }
    .ls-1 { letter-spacing: 0.5px; }
    .education-item, .experience-item, .skill-item { border-left: 4px solid var(--primary-color); }
    .btn-white { background-color: white; color: var(--primary-color); }
    /* Select2 Restyling for bg-light */
    .select2-container .select2-selection--single {
        background-color: #f8f9fa !important;
        border: 0 !important;
        border-radius: 0.5rem !important;
        height: 42px !important;
        padding-top: 6px;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        top: 8px !important;
        right: 8px !important;
    }
</style>
@endpush

@section('content')
<div class="py-5 bg-light" style="min-height: 100vh; padding-top: 120px !important;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card border-0 shadow-lg overflow-hidden rounded-4">
                    <div class="row g-0">
                        <!-- Left Side: Interactive Form -->
                        <div class="col-lg-8 bg-white p-4 p-md-5">
                            <div class="mb-5">
                                <h1 class="fw-bold fs-2 text-dark mb-2">Lengkapi Profil Anda</h1>
                                <p class="text-muted">Lengkapi seluruh data di bawah ini untuk mengaktifkan fitur pencarian kerja.</p>
                                <div class="alert alert-warning border-0 rounded-3 d-flex align-items-center mt-4">
                                    <i class="material-icons me-3">info</i>
                                    <span class="small fw-medium">Pastikan data yang Anda masukkan sesuai dengan KTP asli untuk memudahkan proses verifikasi oleh HRD.</span>
                                </div>
                            </div>

                            <form id="form_complete_data" class="row g-4" enctype="multipart/form-data">
                                @csrf
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary-theme text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 14px;">1</div>
                                        <h5 class="fw-bold text-dark mb-0">Identitas & Domisili</h5>
                                    </div>
                                    <hr class="mt-0 mb-4 opacity-10">
                                </div>

                                <script type="application/json" id="jobs-data">
                                    {!! json_encode($pelamar) !!}
                                </script>

                                <script>
                                    const jobs = JSON.parse(
                                        document.getElementById('jobs-data').textContent
                                    );

                                    console.dir(jobs);
                                </script>

                                <div class="col-12 mb-2 d-flex align-items-center gap-4">
                                    <div class="position-relative">
                                        <div class="rounded-4 overflow-hidden shadow-sm bg-light d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                            @if($pelamar && $pelamar->foto)
                                                <img src="{{ asset('storage/'.$pelamar->foto) }}" id="preview_foto" class="w-100 h-100 object-fit-cover">
                                            @else
                                                <i class="material-icons opacity-25" style="font-size: 4rem;">account_circle</i>
                                                <img id="preview_foto" class="w-100 h-100 object-fit-cover d-none">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form-label fw-bold small text-uppercase ls-1">Foto Profil <span class="text-danger small">(Wajib)</span></label>
                                        <input type="file" name="foto_profil" id="foto_profil" class="form-control bg-light border-0 py-2 rounded-3" accept="image/*" {{ !$pelamar || !$pelamar->foto ? 'required' : '' }}>
                                        <div class="fs-10 text-muted mt-1">Gunakan foto formal (Jas/Kemeja). Maks. 1MB. Format: JPG, PNG.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Nomor KTP (NIK) <span class="text-danger">*</span></label>
                                    <input type="text" name="noktp" class="form-control bg-light border-0 py-2 rounded-3" placeholder="16 Digit NIK" maxlength="16" required value="{{ old('noktp', $pelamar->noktp ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="namalengkap" class="form-control bg-light border-0 py-2 rounded-3" value="{{ old('namalengkap', $pelamar->namalengkap ?? auth()->user()->name) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                                    <input type="text" name="nohp" class="form-control bg-light border-0 py-2 rounded-3" placeholder="08xxxxxxxxxx" value="{{ old('nohp', $pelamar->nohp ?? '') }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" name="tempatlahir" class="form-control bg-light border-0 py-2 rounded-3" value="{{ old('tempatlahir', $pelamar->tempatlahir ?? '') }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggallahir" class="form-control bg-light border-0 py-2 rounded-3" value="{{ old('tanggallahir', $pelamar->tanggallahir ?? '') }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="jeniskelamin" class="form-select bg-light border-0 py-2 rounded-3" required>
                                        <option value="">Pilih</option>
                                        <option value="Laki-laki" {{ ($pelamar->jeniskelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ ($pelamar->jeniskelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Tinggi (cm)</label>
                                    <input type="number" name="tinggibadan" class="form-control bg-light border-0 py-2 rounded-3" value="{{ old('tinggibadan', $pelamar->tinggibadan ?? '') }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Berat (kg)</label>
                                    <input type="number" name="beratbadan" class="form-control bg-light border-0 py-2 rounded-3" value="{{ old('beratbadan', $pelamar->beratbadan ?? '') }}">
                                </div>

                                @php
                                    $currentKelurahan = $pelamar ? $pelamar->kelurahan : null;
                                    $currentKecamatan = $currentKelurahan ? $currentKelurahan->kecamatan : null;
                                    $currentKota = $currentKecamatan ? $currentKecamatan->kota : null;
                                    $currentProvinsi = $currentKota ? $currentKota->provinsi : null;
                                @endphp

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Provinsi <span class="text-danger">*</span></label>
                                    <select id="provinsi_id" class="form-select select2-location bg-light border-0 py-2 rounded-3" required>
                                        <option value="">Pilih Provinsi</option>
                                        @foreach($provinsis as $p)
                                            <option value="{{ $p->id }}" {{ ($currentProvinsi->id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Kota/Kabupaten <span class="text-danger">*</span></label>
                                    <select id="kota_id" class="form-select select2-location bg-light border-0 py-2 rounded-3" required {{ !$currentKota ? 'disabled' : '' }}>
                                        <option value="">Pilih Kota</option>
                                        @if($currentKota)
                                            <option value="{{ $currentKota->id }}" selected>{{ $currentKota->nama }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Kecamatan <span class="text-danger">*</span></label>
                                    <select id="kecamatan_id" class="form-select select2-location bg-light border-0 py-2 rounded-3" required {{ !$currentKecamatan ? 'disabled' : '' }}>
                                        <option value="">Pilih Kecamatan</option>
                                        @if($currentKecamatan)
                                            <option value="{{ $currentKecamatan->id }}" selected>{{ $currentKecamatan->nama }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Kelurahan <span class="text-danger">*</span></label>
                                    <select name="idkelurahan" id="kelurahan_id" class="form-select select2-location bg-light border-0 py-2 rounded-3" required {{ !$currentKelurahan ? 'disabled' : '' }}>
                                        <option value="">Pilih Kelurahan</option>
                                        @if($currentKelurahan)
                                            <option value="{{ $currentKelurahan->id }}" selected>{{ $currentKelurahan->nama }}</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-uppercase ls-1">Alamat Lengkap <span class="text-danger">*</span></label>
                                    <textarea name="alamatlengkap" class="form-control bg-light border-0 py-2 rounded-3" rows="2" required placeholder="Nama jalan, nomor rumah...">{{ old('alamatlengkap', $pelamar->alamatlengkap ?? '') }}</textarea>
                                </div>

                                <!-- Section 2: Pendidikan -->
                                <div class="col-12 mt-5">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary-theme text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 14px;">2</div>
                                            <h5 class="fw-bold text-dark mb-0">Riwayat Pendidikan</h5>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addEducation()">
                                            <i class="material-icons fs-6 align-middle me-1">add</i> Tambah
                                        </button>
                                    </div>
                                    <hr class="mt-0 mb-4 opacity-10">
                                    <div id="education_container" class="vstack gap-3">
                                        @forelse($pelamar->pendidikans ?? [] as $edu)
                                            <div class="education-item bg-light p-3 rounded-3 position-relative">
                                                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 fs-9" onclick="this.parentElement.remove()"></button>
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Tingkat <span class="text-danger">*</span></label>
                                                        <select name="edu_kategori[]" class="form-select border-0 py-2" required>
                                                            <option value="SD" {{ $edu->kategori == 'SD' ? 'selected' : '' }}>SD</option>
                                                            <option value="SMP" {{ $edu->kategori == 'SMP' ? 'selected' : '' }}>SMP</option>
                                                            <option value="SMA/SMK" {{ $edu->kategori == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                                            <option value="D3" {{ $edu->kategori == 'D3' ? 'selected' : '' }}>D3</option>
                                                            <option value="D4/S1" {{ $edu->kategori == 'D4/S1' ? 'selected' : '' }}>D4/S1</option>
                                                            <option value="S2" {{ $edu->kategori == 'S2' ? 'selected' : '' }}>S2</option>
                                                            <option value="S3" {{ $edu->kategori == 'S3' ? 'selected' : '' }}>S3</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Nama Institusi <span class="text-danger">*</span></label>
                                                        <input type="text" name="edu_nama[]" class="form-control border-0 py-2" value="{{ $edu->namasekolah }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Jurusan</label>
                                                        <input type="text" name="edu_jurusan[]" class="form-control border-0 py-2" value="{{ $edu->jurusan }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Tahun Masuk <span class="text-danger">*</span></label>
                                                        <input type="number" name="edu_awal[]" class="form-control border-0 py-2" value="{{ $edu->tahunawal }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Tahun Lulus <span class="text-danger">*</span></label>
                                                        <input type="number" name="edu_akhir[]" class="form-control border-0 py-2" value="{{ $edu->tahunselesai }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="education-item bg-light p-3 rounded-3 position-relative">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Tingkat <span class="text-danger">*</span></label>
                                                        <select name="edu_kategori[]" class="form-select border-0 py-2" required>
                                                            <option value="SD">SD</option>
                                                            <option value="SMP">SMP</option>
                                                            <option value="SMA/SMK">SMA/SMK</option>
                                                            <option value="D3">D3</option>
                                                            <option value="D4/S1">D4/S1</option>
                                                            <option value="S2">S2</option>
                                                            <option value="S3">S3</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Nama Institusi <span class="text-danger">*</span></label>
                                                        <input type="text" name="edu_nama[]" class="form-control border-0 py-2" placeholder="Contoh: Universitas Indonesia" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Jurusan</label>
                                                        <input type="text" name="edu_jurusan[]" class="form-control border-0 py-2" placeholder="Contoh: Teknik Informatika">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Tahun Masuk <span class="text-danger">*</span></label>
                                                        <input type="number" name="edu_awal[]" class="form-control border-0 py-2" placeholder="YYYY" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Tahun Lulus <span class="text-danger">*</span></label>
                                                        <input type="number" name="edu_akhir[]" class="form-control border-0 py-2" placeholder="YYYY" required>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Section 3: Keahlian -->
                                <div class="col-12 mt-5">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary-theme text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 14px;">3</div>
                                            <h5 class="fw-bold text-dark mb-0">Keahlian / Skills</h5>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addSkill()">
                                            <i class="material-icons fs-6 align-middle me-1">add</i> Tambah
                                        </button>
                                    </div>
                                    <hr class="mt-0 mb-4 opacity-10">
                                    <div id="skill_container" class="vstack gap-3">
                                        @forelse($pelamar->skills ?? [] as $skill)
                                            <div class="skill-item bg-light p-3 rounded-3 position-relative">
                                                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 fs-9" onclick="this.parentElement.remove()"></button>
                                                <div class="row g-3">
                                                    <div class="col-md-7">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Nama Keahlian <span class="text-danger">*</span></label>
                                                        <input type="text" name="skill_nama[]" class="form-control border-0 py-2" value="{{ $skill->namaskill }}" required>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Tingkat Penguasaan <span class="text-danger">*</span></label>
                                                        <select name="skill_ket[]" class="form-select border-0 py-2" required>
                                                            <option value="Kurang" {{ $skill->keterangan == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                                            <option value="Cukup" {{ $skill->keterangan == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                                            <option value="Baik" {{ $skill->keterangan == 'Baik' ? 'selected' : '' }}>Baik</option>
                                                            <option value="Sangat Baik" {{ $skill->keterangan == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="skill-item bg-light p-3 rounded-3 position-relative">
                                                <div class="row g-3">
                                                    <div class="col-md-7">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Nama Keahlian <span class="text-danger">*</span></label>
                                                        <input type="text" name="skill_nama[]" class="form-control border-0 py-2" placeholder="Contoh: Microsoft Excel / Programming" required>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Tingkat Penguasaan <span class="text-danger">*</span></label>
                                                        <select name="skill_ket[]" class="form-select border-0 py-2" required>
                                                            <option value="">Pilih Tingkat</option>
                                                            <option value="Kurang">Kurang</option>
                                                            <option value="Cukup">Cukup</option>
                                                            <option value="Baik">Baik</option>
                                                            <option value="Sangat Baik">Sangat Baik</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Section 4: Pengalaman -->
                                <div class="col-12 mt-5">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary-theme text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 14px;">4</div>
                                            <h5 class="fw-bold text-dark mb-0">Pengalaman Kerja</h5>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="checkbox" name="no_experience" value="1" id="no_experience" {{ ($pelamar && $pelamar->pengalamans->count() == 0) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold small text-muted text-uppercase ls-1" for="no_experience">
                                                    Belum Memiliki Pengalaman
                                                </label>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btn_add_experience" onclick="addExperience()">
                                                <i class="material-icons fs-6 align-middle me-1">add</i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                    <hr class="mt-0 mb-4 opacity-10">
                                    <div id="experience_container" class="vstack gap-3" style="{{ ($pelamar && $pelamar->pengalamans->count() == 0) ? 'display:none' : '' }}">
                                        @forelse($pelamar->pengalamans ?? [] as $index => $exp)
                                            <div class="experience-item bg-light p-3 rounded-3 position-relative">
                                                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 fs-9" onclick="this.parentElement.remove()"></button>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Nama Perusahaan <span class="text-danger">*</span></label>
                                                        <input type="text" name="exp_nama[]" class="form-control border-0 py-2" value="{{ $exp->namaperusahaan }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Posisi/Jabatan <span class="text-danger">*</span></label>
                                                        <input type="text" name="exp_posisi[]" class="form-control border-0 py-2" value="{{ $exp->posisi }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Tahun Masuk <span class="text-danger">*</span></label>
                                                        <input type="number" name="exp_awal[]" class="form-control border-0 py-2" value="{{ $exp->tahunawal }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Tahun Selesai</label>
                                                        <input type="number" name="exp_akhir[]" class="form-control border-0 py-2" value="{{ $exp->tahunselesai }}">
                                                    </div>
                                                    <div class="col-md-4 d-flex align-items-end pb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="exp_aktif[]" value="1" id="exp_aktif_{{ $index }}" {{ $exp->aktif ? 'checked' : '' }}>
                                                            <label class="form-check-label small text-muted" for="exp_aktif_{{ $index }}">Masih Bekerja</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="experience-item bg-light p-3 rounded-3 position-relative">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Nama Perusahaan <span class="text-danger">*</span></label>
                                                        <input type="text" name="exp_nama[]" class="form-control border-0 py-2" placeholder="Contoh: PT Maju Jaya" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Posisi/Jabatan <span class="text-danger">*</span></label>
                                                        <input type="text" name="exp_posisi[]" class="form-control border-0 py-2" placeholder="Contoh: Staff Admin" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Tahun Masuk <span class="text-danger">*</span></label>
                                                        <input type="number" name="exp_awal[]" class="form-control border-0 py-2" placeholder="YYYY" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fs-9 fw-bold text-uppercase">Tahun Selesai</label>
                                                        <input type="number" name="exp_akhir[]" class="form-control border-0 py-2" placeholder="YYYY (Kosongkan jika aktif)">
                                                    </div>
                                                    <div class="col-md-4 d-flex align-items-end pb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="exp_aktif[]" value="1" id="exp_aktif_0">
                                                            <label class="form-check-label small text-muted" for="exp_aktif_0">Masih Bekerja</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Section 5: Dokumen -->
                                <div class="col-12 mt-5">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary-theme text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 14px;">5</div>
                                            <h5 class="fw-bold text-dark mb-0">Dokumen Pendukung</h5>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addDocument()">
                                            <i class="material-icons fs-6 align-middle me-1">add</i> Tambah Dokumen Lain
                                        </button>
                                    </div>
                                    <hr class="mt-0 mb-4 opacity-10">
                                    <div id="document_container" class="row g-3">
                                        @php
                                            $ktpDoc = $pelamar ? $pelamar->dokumens->where('namadokumen', 'KTP')->first() : null;
                                            $kuningDoc = $pelamar ? $pelamar->dokumens->where('namadokumen', 'Kartu Kuning (AK-1)')->first() : null;
                                            $additionalDocs = $pelamar ? $pelamar->dokumens->whereNotIn('namadokumen', ['KTP', 'Kartu Kuning (AK-1)']) : collect();
                                        @endphp
                                        
                                        <!-- Mandatory KTP -->
                                        <div class="col-md-6">
                                            <div class="bg-light p-3 rounded-3 h-100">
                                                <label class="form-label fs-9 fw-bold text-uppercase">Kartu Tanda Penduduk (KTP) {{ !$ktpDoc ? '*' : '' }}</label>
                                                <input type="file" name="doc_file_ktp" class="form-control border-0 py-2" accept=".pdf,.jpg,.jpeg,.png" {{ !$ktpDoc ? 'required' : '' }}>
                                                @if($ktpDoc)
                                                    <div class="mt-2 text-primary small d-flex align-items-center">
                                                        <i class="material-icons fs-6 me-1">verified</i> <a href="{{ asset('storage/'.$ktpDoc->filedokumen) }}" target="_blank" class="text-decoration-none">Lihat KTP Terunggah</a>
                                                    </div>
                                                @else
                                                    <div class="fs-10 text-muted mt-1">Format: PDF/JPG (Maks. 2MB)</div>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Mandatory Kartu Kuning -->
                                        <div class="col-md-6">
                                            <div class="bg-light p-3 rounded-3 h-100">
                                                <label class="form-label fs-9 fw-bold text-uppercase">Kartu Kuning (AK-1) {{ !$kuningDoc ? '*' : '' }}</label>
                                                <input type="file" name="doc_file_kuning" class="form-control border-0 py-2" accept=".pdf,.jpg,.jpeg,.png" {{ !$kuningDoc ? 'required' : '' }}>
                                                @if($kuningDoc)
                                                    <div class="mt-2 text-primary small d-flex align-items-center">
                                                        <i class="material-icons fs-6 me-1">verified</i> <a href="{{ asset('storage/'.$kuningDoc->filedokumen) }}" target="_blank" class="text-decoration-none">Lihat Kartu Kuning</a>
                                                    </div>
                                                @else
                                                    <div class="fs-10 text-muted mt-1">Format: PDF/JPG (Maks. 2MB)</div>
                                                @endif
                                            </div>
                                        </div>

                                        @foreach($additionalDocs as $doc)
                                            <div class="col-md-6 additional-doc-item">
                                                <div class="bg-light p-3 rounded-3 h-100 position-relative">
                                                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2 fs-9" onclick="this.parentElement.parentElement.remove()"></button>
                                                    <label class="form-label fs-9 fw-bold text-uppercase">Dokumen: {{ $doc->namadokumen }}</label>
                                                    <div class="mb-2">
                                                        <a href="{{ asset('storage/'.$doc->filedokumen) }}" target="_blank" class="btn btn-sm btn-white border px-3 rounded-pill text-primary-theme fs-9 fw-bold">
                                                            <i class="material-icons fs-6 align-middle me-1">visibility</i> Lihat File
                                                        </a>
                                                    </div>
                                                    <!-- Keep existing data for update if needed, but the controller clears & saves new anyway -->
                                                    <input type="hidden" name="existing_doc_name[]" value="{{ $doc->namadokumen }}">
                                                    <input type="hidden" name="existing_doc_file[]" value="{{ $doc->filedokumen }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-12 mt-5">
                                    <button type="submit" class="btn btn-theme w-100 py-3 rounded-pill fw-bold ls-1 shadow-sm" id="btn_submit">
                                        {{ $pelamar ? 'PERBARUI PROFIL & SELESAI' : 'SIMPAN PROFIL & SELESAI' }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Right Side: Sidebar Info -->
                        <div class="col-lg-4 bg-primary-theme p-4 p-md-5 text-white">
                            <div class="mb-5">
                                <h4 class="fw-bold text-warning mb-4">Checklist Profil</h4>
                                <div class="vstack gap-4">
                                    <div class="d-flex align-items-center {{ $pelamar ? 'text-warning' : 'opacity-75' }}" id="check_step_1">
                                        <i class="material-icons me-3">{{ $pelamar ? 'verified' : 'pending' }}</i>
                                        <span class="small fw-medium">Identitas & Wilayah</span>
                                    </div>
                                    <div class="d-flex align-items-center {{ ($pelamar && $pelamar->pendidikans->count()) ? 'text-warning' : 'opacity-75' }}" id="check_step_2">
                                        <i class="material-icons me-3">{{ ($pelamar && $pelamar->pendidikans->count()) ? 'verified' : 'pending' }}</i>
                                        <span class="small fw-medium">Pendidikan Terakhir</span>
                                    </div>
                                    <div class="d-flex align-items-center {{ ($pelamar && $pelamar->skills->count()) ? 'text-warning' : 'opacity-75' }}" id="check_step_3">
                                        <i class="material-icons me-3">{{ ($pelamar && $pelamar->skills->count()) ? 'verified' : 'pending' }}</i>
                                        <span class="small fw-medium">Keahlian / Skills</span>
                                    </div>
                                    <div class="d-flex align-items-center {{ ($pelamar && $pelamar->pengalamans->count()) ? 'text-warning' : 'opacity-75' }}" id="check_step_4">
                                        <i class="material-icons me-3">{{ ($pelamar && $pelamar->pengalamans->count()) ? 'verified' : 'pending' }}</i>
                                        <span class="small fw-medium">Pengalaman Kerja</span>
                                    </div>
                                    <div class="d-flex align-items-center {{ ($ktpDoc && $kuningDoc) ? 'text-warning' : 'opacity-75' }}" id="check_step_5">
                                        <i class="material-icons me-3">{{ ($ktpDoc && $kuningDoc) ? 'verified' : 'pending' }}</i>
                                        <span class="small fw-medium">Upload Dokumen Wajib</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto pt-5 border-top border-white border-opacity-10">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="material-icons text-warning me-2">security</i>
                                    <span class="small fw-bold">Privasi Terjamin</span>
                                </div>
                                <p class="fs-10 opacity-60">Data Anda hanya akan dibagikan kepada perusahaan yang Anda lamar. Kami menjamin kerahasiaan informasi sensitif Anda sesuai standar keamanan data.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.buttons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    function notify(type, title, text) {
        new PNotify({
            title: title,
            text: text,
            type: type,
            styling: 'brighttheme',
            delay: 4000
        });
    }

    function addEducation() {
        const html = `
            <div class="education-item bg-light p-3 rounded-3 position-relative mt-2">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 fs-9" onclick="this.parentElement.remove()"></button>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fs-9 fw-bold text-uppercase">Tingkat <span class="text-danger">*</span></label>
                        <select name="edu_kategori[]" class="form-select border-0 py-2" required>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA/SMK">SMA/SMK</option>
                            <option value="D3">D3</option>
                            <option value="D4/S1">D4/S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fs-9 fw-bold text-uppercase">Nama Institusi <span class="text-danger">*</span></label>
                        <input type="text" name="edu_nama[]" class="form-control border-0 py-2" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-9 fw-bold text-uppercase">Jurusan</label>
                        <input type="text" name="edu_jurusan[]" class="form-control border-0 py-2">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-9 fw-bold text-uppercase">Tahun Masuk <span class="text-danger">*</span></label>
                        <input type="number" name="edu_awal[]" class="form-control border-0 py-2" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-9 fw-bold text-uppercase">Tahun Lulus <span class="text-danger">*</span></label>
                        <input type="number" name="edu_akhir[]" class="form-control border-0 py-2" required>
                    </div>
                </div>
            </div>`;
        $('#education_container').append(html);
    }

    function addSkill() {
        const html = `
            <div class="skill-item bg-light p-3 rounded-3 position-relative mt-2">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 fs-9" onclick="this.parentElement.remove()"></button>
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label fs-9 fw-bold text-uppercase">Nama Keahlian <span class="text-danger">*</span></label>
                        <input type="text" name="skill_nama[]" class="form-control border-0 py-2" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fs-9 fw-bold text-uppercase">Tingkat Penguasaan <span class="text-danger">*</span></label>
                        <select name="skill_ket[]" class="form-select border-0 py-2" required>
                            <option value="Kurang">Kurang</option>
                            <option value="Cukup">Cukup</option>
                            <option value="Baik">Baik</option>
                            <option value="Sangat Baik">Sangat Baik</option>
                        </select>
                    </div>
                </div>
            </div>`;
        $('#skill_container').append(html);
    }

    function addExperience() {
        const count = $('.experience-item').length;
        const html = `
            <div class="experience-item bg-light p-3 rounded-3 position-relative mt-2">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 fs-9" onclick="this.parentElement.remove()"></button>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fs-9 fw-bold text-uppercase">Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" name="exp_nama[]" class="form-control border-0 py-2" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-9 fw-bold text-uppercase">Posisi/Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="exp_posisi[]" class="form-control border-0 py-2" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-9 fw-bold text-uppercase">Tahun Masuk <span class="text-danger">*</span></label>
                        <input type="number" name="exp_awal[]" class="form-control border-0 py-2" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-9 fw-bold text-uppercase">Tahun Selesai</label>
                        <input type="number" name="exp_akhir[]" class="form-control border-0 py-2">
                    </div>
                    <div class="col-md-4 d-flex align-items-end pb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="exp_aktif[]" value="1" id="exp_aktif_${count}">
                            <label class="form-check-label small text-muted" for="exp_aktif_${count}">Masih Bekerja</label>
                        </div>
                    </div>
                </div>
            </div>`;
        $('#experience_container').append(html);
    }

    function addDocument() {
        const html = `
            <div class="col-md-6">
                <div class="bg-light p-3 rounded-3 h-100 position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2 fs-9" onclick="this.parentElement.parentElement.remove()"></button>
                    <label class="form-label fs-9 fw-bold text-uppercase">Nama Dokumen Tambahan</label>
                    <input type="text" name="doc_name[]" class="form-control border-0 py-2 mb-2" placeholder="Contoh: Sertifikat Keahlian" required>
                    <input type="file" name="doc_file[]" class="form-control border-0 py-2" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>
            </div>`;
        $('#document_container').append(html);
    }

    $(document).ready(function() {
        // Initialize Select2 for Locales
        $('.select2-location').select2({
            width: '100%',
        });

        // Location AJAX
        $('#foto_profil').change(function() {
            const file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_foto').attr('src', e.target.result).removeClass('d-none');
                    $('.material-icons.opacity-25').addClass('d-none');
                }
                reader.readAsDataURL(file);
            }
        });

        $('#provinsi_id').change(function() {
            const id = $(this).val();
            $('#kota_id').html('<option value="">Pilih Kota</option>').attr('disabled', true).trigger('change.select2');
            $('#kecamatan_id').html('<option value="">Pilih Kecamatan</option>').attr('disabled', true).trigger('change.select2');
            $('#kelurahan_id').html('<option value="">Pilih Kelurahan</option>').attr('disabled', true).trigger('change.select2');
            if (id) {
                $.get("{{ url('/perusahaan/registration/kotas') }}/" + id, function(response) {
                    let options = '<option value="">Pilih Kota</option>';
                    if (response.results) {
                        response.results.forEach(item => options += `<option value="${item.id}">${item.text}</option>`);
                    }
                    $('#kota_id').html(options).attr('disabled', false).trigger('change.select2');
                });
            }
        });

        $('#kota_id').change(function() {
            const id = $(this).val();
            $('#kecamatan_id').html('<option value="">Pilih Kecamatan</option>').attr('disabled', true).trigger('change.select2');
            $('#kelurahan_id').html('<option value="">Pilih Kelurahan</option>').attr('disabled', true).trigger('change.select2');
            if (id) {
                $.get("{{ url('/perusahaan/registration/kecamatans') }}/" + id, function(response) {
                    let options = '<option value="">Pilih Kecamatan</option>';
                    if (response.results) {
                        response.results.forEach(item => options += `<option value="${item.id}">${item.text}</option>`);
                    }
                    $('#kecamatan_id').html(options).attr('disabled', false).trigger('change.select2');
                });
            }
        });

        $('#kecamatan_id').change(function() {
            const id = $(this).val();
            $('#kelurahan_id').html('<option value="">Pilih Kelurahan</option>').attr('disabled', true).trigger('change.select2');
            if (id) {
                $.get("{{ url('/perusahaan/registration/kelurahans') }}/" + id, function(response) {
                    let options = '<option value="">Pilih Kelurahan</option>';
                    if (response.results) {
                        response.results.forEach(item => options += `<option value="${item.id}">${item.text}</option>`);
                    }
                    $('#kelurahan_id').html(options).attr('disabled', false).trigger('change.select2');
                });
            }
        });

        // Toggle Experience
        $('#no_experience').change(function() {
            if ($(this).is(':checked')) {
                $('#experience_container').fadeOut();
                $('#btn_add_experience').fadeOut();
                $('#experience_container').find("input, select").prop('required', false);
            } else {
                $('#experience_container').fadeIn();
                $('#btn_add_experience').fadeIn();
                $('#experience_container').find("input, select").prop('required', true);
                if ($('.experience-item').length === 0) {
                    addExperience();
                }
            }
        }).trigger('change');

        // Form Submit
        $('#form_complete_data').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            $('.is-invalid-custom').removeClass('is-invalid-custom');
            
            let isValid = true;
            $(this).find('[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid-custom');
                    isValid = false;
                }
            });

            if (!isValid) {
                notify('error', 'Formulir Belum Lengkap', 'Silakan lengkapi bidang yang ditandai merah.');
                return;
            }

            const btn = $('#btn_submit');
            const originalText = btn.html();
            
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> MENYIMPAN PROFIL...');
            
            const formData = new FormData(this);
            
            $.ajax({
                url: "{{ route('pelamar.complete-data.post') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Profil Berhasil Disimpan!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        willClose: () => {
                            window.location.href = response.redirect;
                        }
                    });
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    let error = 'Terjadi kesalahan sistem.';
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        error = Object.values(errors)[0][0];
                        
                        // Map backend errors to red borders if possible
                        Object.keys(errors).forEach(key => {
                            $(`[name="${key}"], [name="${key}[]"]`).addClass('is-invalid-custom');
                        });
                    }
                    notify('error', 'Gagal Menyimpan', error);
                }
            });
        });

        $(document).on('input change', '.is-invalid-custom', function() {
            $(this).removeClass('is-invalid-custom');
        });
    });
</script>
<style>
    .fs-9 { font-size: 0.75rem; }
    .fs-10 { font-size: 0.65rem; }
    .ls-1 { letter-spacing: 0.5px; }
    .education-item, .experience-item, .skill-item { border-left: 4px solid var(--primary-color); }
    .btn-white { background-color: white; color: var(--primary-color); }
</style>
@endpush

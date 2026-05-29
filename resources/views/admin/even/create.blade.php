@extends('layouts.admin')

@section('title', 'Tambah Event')
@section('page_title', 'Buat Event Job Fair Baru')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<style>
    #map { height: 300px; border-radius: 8px; margin-top: 10px; border: 1px solid #e1e3ea; }
</style>
@endpush

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">Form Input Event</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('admin.event') }}" class="btn btn-light-primary btn-sm">
                <i class="material-icons fs-5 me-1">arrow_back</i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form id="event-form" action="{{ route('admin.event.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row g-9 mb-8">
                <div class="col-md-12 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Nama Periode / Event</label>
                    <input type="text" name="namaperiode" class="form-control form-control-solid" placeholder="Contoh: Job Fair Maret 2026" required />
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-12 fv-row">
                    <label class="fs-6 fw-bold mb-2">Visi Event</label>
                    <textarea name="visi" class="form-control form-control-solid" rows="2" placeholder="Masukkan visi atau tujuan event..."></textarea>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Tanggal Mulai</label>
                    <div class="position-relative d-flex align-items-center">
                        <i class="material-icons position-absolute ms-4 text-gray-500">calendar_today</i>
                        <input type="date" name="tanggalawal" class="form-control form-control-solid ps-12" required />
                    </div>
                </div>
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Tanggal Selesai</label>
                    <div class="position-relative d-flex align-items-center">
                        <i class="material-icons position-absolute ms-4 text-gray-500">event_busy</i>
                        <input type="date" name="tanggalselesai" class="form-control form-control-solid ps-12" required />
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-12 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Lokasi Pelaksanaan (Singkat)</label>
                    <input type="text" name="lokasi" class="form-control form-control-solid" placeholder="Contoh: Convention Hall lt. 3" required />
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-12 fv-row">
                    <label class="fs-6 fw-bold mb-2">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" class="form-control form-control-solid" rows="2" placeholder="Masukkan alamat lengkap lokasi..."></textarea>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-bold mb-2">Latitude</label>
                    <input type="text" name="latitude" id="latitude" class="form-control form-control-solid" placeholder="-6.123456" />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-bold mb-2">Longitude</label>
                    <input type="text" name="longitude" id="longitude" class="form-control form-control-solid" placeholder="106.123456" />
                </div>
                <div class="col-md-12">
                    <div id="map"></div>
                    <div class="form-text mt-2"><i class="material-icons fs-7 align-middle">info</i> Klik pada peta atau geser penanda untuk memilih lokasi</div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Kuota Maksimum (Total)</label>
                    <input type="number" name="kuota_maksimum" class="form-control form-control-solid" placeholder="0 untuk tidak terbatas" min="0" value="0" required />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Maksimum Apply per Pelamar</label>
                    <input type="number" name="maksimum_apply" class="form-control form-control-solid" placeholder="0 untuk tidak terbatas" min="0" value="0" required />
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-12 fv-row">
                    <label class="fs-6 fw-bold mb-2">Keterangan / Deskripsi</label>
                    <textarea name="keterangan" class="form-control form-control-solid" rows="3" placeholder="Tambahkan rincian event jika ada..."></textarea>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-bold mb-2">Poster / Gambar Event</label>
                    <input type="file" name="gambar" id="poster_input" class="form-control form-control-solid" accept="image/*" />
                    <div class="form-text">Format: JPG, PNG, GIF. Maks: 2MB</div>
                    <div id="poster_preview_container" class="mt-3" style="display: none;">
                        <img id="poster_preview" src="#" class="img-thumbnail" style="max-height: 150px;">
                    </div>
                </div>
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-bold mb-2">Gambar Layout / Denah</label>
                    <input type="file" name="gambar_layout" id="layout_input" class="form-control form-control-solid" accept="image/*" />
                    <div class="form-text">Format: JPG, PNG, GIF. Maks: 2MB</div>
                    <div id="layout_preview_container" class="mt-3" style="display: none;">
                        <img id="layout_preview" src="#" class="img-thumbnail" style="max-height: 150px;">
                    </div>
                </div>
            <div class="row g-9 mb-8">
                <div class="col-md-4 fv-row">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-30px w-50px" type="checkbox" name="statusaktif" id="statusaktif" checked />
                        <label class="form-check-label fw-bold text-gray-700 ms-3" for="statusaktif">
                            Event Aktif
                        </label>
                    </div>
                </div>
                <div class="col-md-4 fv-row">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-30px w-50px" type="checkbox" name="statusheadline" id="statusheadline" />
                        <label class="form-check-label fw-bold text-gray-700 ms-3" for="statusheadline">
                            Jadikan Headline
                        </label>
                    </div>
                </div>
                <div class="col-md-4 fv-row">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-30px w-50px" type="checkbox" name="statuspaket" id="statuspaket" value="1" />
                        <label class="form-check-label fw-bold text-gray-700 ms-3" for="statuspaket">
                            Gunakan Paket & Fasilitas
                        </label>
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8" id="biaya-container">
                <div class="col-md-12 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Biaya Pendaftaran Flat (Non-Paket)</label>
                    <div class="input-group input-group-solid">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="biaya" class="form-control" placeholder="0" min="0" required />
                    </div>
                    <div class="form-text text-danger mt-2">Karena pendaftaran tanpa paket, biaya flat wajib ditentukan.</div>
                </div>
            </div>

            <div id="packet-wrapper" style="display: none;">
                <div class="mb-5 d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark">Data Paket Partisipasi</h3>
                        <div class="text-muted fw-semibold fs-7">Tentukan berbagai pilihan paket untuk perusahaan (Contoh: Gold, Silver, dll)</div>
                    </div>
                    <button type="button" class="btn btn-light-info btn-sm btn-flex btn-center" id="add-packet">
                        <i class="material-icons fs-5 me-1">add_box</i> Tambah Paket
                    </button>
                </div>
                <div id="packet-container"></div>
                <div class="separator my-10 border-gray-300"></div>
            </div>

            <div class="row g-9 mb-8">
                 <div class="col-md-12 fv-row">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-30px w-50px" type="checkbox" name="status_sesi" id="status_sesi" value="1" />
                        <label class="form-check-label fw-bold text-gray-700 ms-3" for="status_sesi">
                            Gunakan Sesi (Event Multi-Hari)
                        </label>
                    </div>
                </div>
            </div>

            <div id="session-wrapper" style="display: none;">
                <div class="separator my-10 border-primary border-top-1 dotted"></div>
                <div class="mb-5 d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark">Pengaturan Sesi Event</h3>
                        <div class="text-muted fw-semibold fs-7">Tentukan jadwal sesi dan kuota per sesi agar pendaftaran lebih teratur</div>
                    </div>
                    <button type="button" class="btn btn-light-primary btn-sm btn-flex btn-center" id="add-session">
                        <i class="material-icons fs-5 me-1">add_circle</i> Tambah Sesi
                    </button>
                </div>
                <div id="session-container"></div>
            </div>

            <div class="separator my-10 border-gray-300"></div>

            <div class="mb-10">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h3 class="fw-bold text-dark">Data Sponsor Event</h3>
                        <div class="text-muted fw-semibold fs-7">Anda dapat menambahkan lebih dari 1 sponsor untuk event ini</div>
                    </div>
                    <button type="button" class="btn btn-light-success btn-sm btn-flex btn-center" id="add-sponsor">
                        <i class="material-icons fs-5 me-1">loyalty</i> Tambah Sponsor
                    </button>
                </div>
                <div id="sponsor-container">
                    <!-- Dynamic Sponsor Rows -->
                </div>
            </div>

            <div class="separator my-10"></div>

            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-light me-3">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <span class="indicator-label">Simpan Event</span>
                    <span class="indicator-progress">Mohon tunggu... 
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin/event.js') }}"></script>
@endpush
@endsection

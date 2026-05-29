@extends('layouts.admin')

@section('title', 'Edit Event')
@section('page_title', 'Perbarui Data Event')

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
            <h3 class="fw-bold">Edit Event: {{ $event->namaperiode }}</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('admin.event') }}" class="btn btn-light-primary btn-sm">
                <i class="material-icons fs-5 me-1">arrow_back</i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form id="event-form" action="{{ route('admin.event.update', $event->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-9 mb-8">
                <div class="col-md-12 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Nama Periode / Event</label>
                    <input type="text" name="namaperiode" class="form-control form-control-solid" value="{{ $event->namaperiode }}" required />
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-12 fv-row">
                    <label class="fs-6 fw-bold mb-2">Visi Event</label>
                    <textarea name="visi" class="form-control form-control-solid" rows="2">{{ $event->visi }}</textarea>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Tanggal Mulai</label>
                    <div class="position-relative d-flex align-items-center">
                        <i class="material-icons position-absolute ms-4 text-gray-500">calendar_today</i>
                        <input type="date" name="tanggalawal" class="form-control form-control-solid ps-12" value="{{ $event->tanggalawal }}" required />
                    </div>
                </div>
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Tanggal Selesai</label>
                    <div class="position-relative d-flex align-items-center">
                        <i class="material-icons position-absolute ms-4 text-gray-500">event_busy</i>
                        <input type="date" name="tanggalselesai" class="form-control form-control-solid ps-12" value="{{ $event->tanggalselesai }}" required />
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-12 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Lokasi Pelaksanaan (Singkat)</label>
                    <input type="text" name="lokasi" class="form-control form-control-solid" value="{{ $event->lokasi }}" required />
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-12 fv-row">
                    <label class="fs-6 fw-bold mb-2">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" class="form-control form-control-solid" rows="2">{{ $event->alamat_lengkap }}</textarea>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-bold mb-2">Latitude</label>
                    <input type="text" name="latitude" id="latitude" class="form-control form-control-solid" value="{{ $event->latitude }}" />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-bold mb-2">Longitude</label>
                    <input type="text" name="longitude" id="longitude" class="form-control form-control-solid" value="{{ $event->longitude }}" />
                </div>
                <div class="col-md-12">
                    <div id="map"></div>
                    <div class="form-text mt-2"><i class="material-icons fs-7 align-middle">info</i> Klik pada peta atau geser penanda untuk memilih lokasi</div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Kuota Maksimum (Total)</label>
                    <input type="number" name="kuota_maksimum" class="form-control form-control-solid" value="{{ $event->kuota_maksimum }}" min="0" required />
                </div>
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Maksimum Apply per Pelamar</label>
                    <input type="number" name="maksimum_apply" class="form-control form-control-solid" value="{{ $event->maksimum_apply }}" min="0" required />
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-12 fv-row">
                    <label class="fs-6 fw-bold mb-2">Keterangan / Deskripsi</label>
                    <textarea name="keterangan" class="form-control form-control-solid" rows="3">{{ $event->keterangan }}</textarea>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-bold mb-2">Poster / Gambar Event</label>
                    <input type="file" name="gambar" id="poster_input" class="form-control form-control-solid" accept="image/*" />
                    <div id="poster_preview_container" class="mt-3">
                        @if($event->gambar)
                            <img id="current_poster" src="{{ asset('storage/' . $event->gambar) }}" class="img-thumbnail" style="max-height: 100px;">
                        @endif
                        <img id="poster_preview" src="#" class="img-thumbnail" style="max-height: 150px; display: none;">
                    </div>
                </div>
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-bold mb-2">Gambar Layout / Denah</label>
                    <input type="file" name="gambar_layout" id="layout_input" class="form-control form-control-solid" accept="image/*" />
                    <div id="layout_preview_container" class="mt-3">
                        @if($event->gambar_layout)
                            <img id="current_layout" src="{{ asset('storage/' . $event->gambar_layout) }}" class="img-thumbnail" style="max-height: 100px;">
                        @endif
                        <img id="layout_preview" src="#" class="img-thumbnail" style="max-height: 150px; display: none;">
                    </div>
                </div>
            <div class="row g-9 mb-8">
                <div class="col-md-4 fv-row">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-30px w-50px" type="checkbox" name="statusaktif" id="statusaktif" {{ $event->statusaktif ? 'checked' : '' }} />
                        <label class="form-check-label fw-bold text-gray-700 ms-3" for="statusaktif">
                            Event Aktif
                        </label>
                    </div>
                </div>
                <div class="col-md-4 fv-row">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-30px w-50px" type="checkbox" name="statusheadline" id="statusheadline" {{ $event->statusheadline ? 'checked' : '' }} />
                        <label class="form-check-label fw-bold text-gray-700 ms-3" for="statusheadline">
                            Jadikan Headline
                        </label>
                    </div>
                </div>
                <div class="col-md-4 fv-row">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-30px w-50px" type="checkbox" name="statuspaket" id="statuspaket" value="1" {{ $event->statuspaket ? 'checked' : '' }} />
                        <label class="form-check-label fw-bold text-gray-700 ms-3" for="statuspaket">
                            Gunakan Paket & Fasilitas
                        </label>
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8" id="biaya-container" style="{{ $event->statuspaket ? 'display: none;' : '' }}">
                <div class="col-md-12 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Biaya Pendaftaran Flat (Non-Paket)</label>
                    <div class="input-group input-group-solid">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="biaya" class="form-control" placeholder="0" min="0" value="{{ (int)$event->biaya }}" {{ !$event->statuspaket ? 'required' : '' }} />
                    </div>
                </div>
            </div>

            <div id="packet-wrapper" style="{{ $event->statuspaket ? '' : 'display: none;' }}">
                <div class="mb-5 d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold text-dark">Data Paket Partisipasi</h3>
                        <div class="text-muted fw-semibold fs-7">Tentukan berbagai pilihan paket untuk perusahaan</div>
                    </div>
                    <button type="button" class="btn btn-light-info btn-sm btn-flex btn-center" id="add-packet">
                        <i class="material-icons fs-5 me-1">add_box</i> Tambah Paket
                    </button>
                </div>
                <div id="packet-container">
                    @if($event->statuspaket)
                        @foreach($event->pakets as $index => $paket)
                        <div class="packet-row mb-5 p-5 border rounded bg-light position-relative animate__animated animate__fadeIn">
                            <button type="button" class="btn btn-icon btn-sm btn-light-danger position-absolute top-0 end-0 m-2 remove-packet" title="Hapus Paket">
                                <i class="material-icons fs-5">close</i>
                            </button>
                            <div class="row g-5">
                                <div class="col-md-4">
                                    <label class="required fs-7 fw-bold mb-2">Nama Paket</label>
                                    <input type="text" name="pakets[{{ $index }}][nama_paket]" class="form-control form-control-sm form-control-solid" value="{{ $paket->nama_paket }}" required />
                                </div>
                                <div class="col-md-5">
                                    <label class="fs-7 fw-bold mb-2">Fasilitas</label>
                                    <input type="text" name="pakets[{{ $index }}][fasilitas]" class="form-control form-control-sm form-control-solid" value="{{ $paket->fasilitas }}" />
                                </div>
                                <div class="col-md-3">
                                    <label class="required fs-7 fw-bold mb-2">Harga</label>
                                    <div class="input-group input-group-sm input-group-solid">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="pakets[{{ $index }}][harga]" class="form-control form-control-sm" value="{{ (int)$paket->harga }}" min="0" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                <div class="separator my-10 border-gray-300"></div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-12 fv-row">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-30px w-50px" type="checkbox" name="status_sesi" id="status_sesi" value="1" {{ $event->status_sesi ? 'checked' : '' }} />
                        <label class="form-check-label fw-bold text-gray-700 ms-3" for="status_sesi">
                            Gunakan Sesi (Event Multi-Hari)
                        </label>
                    </div>
                </div>
            </div>

            <div id="session-wrapper" style="{{ $event->status_sesi ? '' : 'display: none;' }}">
                <div class="separator my-10 border-primary border-top-1 dotted"></div>
                <div class="mb-10">
                    <h3 class="fw-bold text-dark">Pengaturan Sesi Event</h3>
                    <div class="text-muted fw-semibold fs-6">Tentukan jadwal sesi dan kuota per sesi agar pendaftaran lebih teratur</div>
                </div>
                
                <div id="session-container">
                    @if($event->status_sesi)
                        @foreach($event->sesis as $index => $sesi)
                        <div class="session-row mb-5 p-5 border rounded bg-light position-relative">
                            <button type="button" class="btn btn-icon btn-sm btn-light-danger position-absolute top-0 end-0 m-2 remove-session" title="Hapus Sesi">
                                <i class="material-icons fs-5">close</i>
                            </button>
                            <div class="row g-5">
                                <div class="col-md-3">
                                    <label class="required fs-7 fw-bold mb-2">Nama Sesi</label>
                                    <input type="text" name="sesi[{{ $index }}][nama_sesi]" class="form-control form-control-sm form-control-solid" value="{{ $sesi->nama_sesi }}" required />
                                </div>
                                <div class="col-md-3">
                                    <label class="required fs-7 fw-bold mb-2">Jam Mulai</label>
                                    <input type="time" name="sesi[{{ $index }}][jam_mulai]" class="form-control form-control-sm form-control-solid" value="{{ \Carbon\Carbon::parse($sesi->jam_mulai)->format('H:i') }}" required />
                                </div>
                                <div class="col-md-3">
                                    <label class="required fs-7 fw-bold mb-2">Jam Selesai</label>
                                    <input type="time" name="sesi[{{ $index }}][jam_selesai]" class="form-control form-control-sm form-control-solid" value="{{ \Carbon\Carbon::parse($sesi->jam_selesai)->format('H:i') }}" required />
                                </div>
                                <div class="col-md-3">
                                    <label class="required fs-7 fw-bold mb-2">Kuota</label>
                                    <input type="number" name="sesi[{{ $index }}][kuota]" class="form-control form-control-sm form-control-solid" value="{{ $sesi->kuota }}" min="1" required />
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                
                <button type="button" class="btn btn-light-primary btn-sm mt-3 btn-flex btn-center" id="add-session">
                    <i class="material-icons fs-5 me-1">add</i> Tambah Sesi Lainnya
                </button>
            </div>

            <div class="separator my-10"></div>

            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-light me-3">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <span class="indicator-label">Perbarui Event</span>
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

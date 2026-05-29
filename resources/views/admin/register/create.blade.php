@extends('layouts.admin')

@section('title', 'Daftarkan Perusahaan')
@section('page_title', 'Registrasi Perusahaan ke Event')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">Form Registrasi Perusahaan</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('admin.register', ['idperiode' => $idperiode]) }}" class="btn btn-light-primary btn-sm">
                <i class="material-icons fs-5 me-1">arrow_back</i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.register.store') }}" method="POST">
            @csrf
            
            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Pilih Event</label>
                    <select name="idperiode" class="form-select form-select-solid" required>
                        <option value="">-- Pilih Event --</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ $idperiode == $event->id ? 'selected' : '' }}>{{ $event->namaperiode }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Pilih Perusahaan</label>
                    <select name="idperusahaan" class="form-select form-select-solid" required data-control="select2">
                        <option value="">-- Pilih Perusahaan --</option>
                        @foreach($perusahaans as $p)
                            <option value="{{ $p->id }}">{{ $p->namaperusahaan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Pilih Paket</label>
                    <select name="namapaket" class="form-select form-select-solid" required id="paket-select">
                        <option value="">-- Pilih Paket --</option>
                        @foreach($pakets as $paket)
                            <option value="{{ $paket->namapaket }}">{{ $paket->namapaket }} - Rp {{ number_format($paket->biaya, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-bold mb-2">Tanggal Registrasi</label>
                    <input type="date" name="tanggalregister" class="form-control form-control-solid" value="{{ date('Y-m-d') }}" required />
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-12 fv-row">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-30px w-50px" type="checkbox" name="aktivasi" id="aktivasi" checked />
                        <label class="form-check-label fw-bold text-gray-700 ms-3" for="aktivasi">
                            Aktifkan Segera (Aktivasi Akses Perusahaan)
                        </label>
                    </div>
                    <div class="form-text mt-3">Perusahaan yang diaktivasi akan dapat mulai menginput lowongan kerja untuk event ini.</div>
                </div>
            </div>

            <div class="separator my-10"></div>

            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-light me-3">Batal</button>
                <button type="submit" class="btn btn-primary">Daftarkan Perusahaan</button>
            </div>
        </form>
    </div>
</div>
@endsection

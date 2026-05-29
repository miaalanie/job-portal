@extends('layouts.admin')

@section('title', 'Edit Lowongan - ' . $loker->namalowongan)

@section('content')
<div class="row g-7 justify-content-center">
    <!-- Left Column: Manual Form -->
    <div class="col-lg-9">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800 fs-3">Perbarui Lowongan: {{ $loker->namalowongan }}</span>
                    <span class="text-muted fw-semibold fs-7 px-1">Modifikasi detail posisi pekerjaan Anda</span>
                </h3>
            </div>
            <form id="loker-form" action="{{ route('admin.perusahaan.loker.update', encrypt($loker->id)) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-10">
                        <label class="required form-label">Nama Posisi / Lowongan</label>
                        <input type="text" name="namalowongan" class="form-control @error('namalowongan') is-invalid @enderror" placeholder="Contoh: Senior Backend Engineer" value="{{ old('namalowongan', $loker->namalowongan) }}" required>
                        @error('namalowongan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-10">
                        <div class="col-md-6">
                            <label class="required form-label">Kategori Pekerjaan</label>
                            <select name="idkategorilowongan" class="form-select @error('idkategorilowongan') is-invalid @enderror" data-control="select2" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('idkategorilowongan', $loker->idkategorilowongan) == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="required form-label">Penempatan Kerja</label>
                            <select name="kategorilokasi" class="form-select @error('kategorilokasi') is-invalid @enderror" required>
                                <option value="">Pilih Penempatan</option>
                                <option value="Dalam Negeri" {{ old('kategorilokasi', $loker->kategorilokasi) == 'Dalam Negeri' ? 'selected' : '' }}>Dalam Negeri (Domestic)</option>
                                <option value="Luar Negeri" {{ old('kategorilokasi', $loker->kategorilokasi) == 'Luar Negeri' ? 'selected' : '' }}>Luar Negeri (Overseas)</option>
                            </select>
                            @error('kategorilokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-10">
                        <div class="col-md-4">
                            <label class="form-label">Gaji Minimal (Rupiah)</label>
                            <input type="text" name="gaji_awal" class="form-control rupiah-mask" placeholder="Contoh: 5.000.000" value="{{ old('gaji_awal', $loker->gaji_awal ? number_format($loker->gaji_awal, 0, ',', '.') : '') }}">
                            <div class="fs-9 text-muted mt-1">Kosongkan jika dirahasiakan</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gaji Maksimal (Rupiah)</label>
                            <input type="text" name="gaji_akhir" class="form-control rupiah-mask" placeholder="Contoh: 15.000.000" value="{{ old('gaji_akhir', $loker->gaji_akhir ? number_format($loker->gaji_akhir, 0, ',', '.') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kuota (Jumlah Rekrutmen)</label>
                            <input type="number" name="kuota" class="form-control" placeholder="Kosongkan jika tak terbatas" value="{{ old('kuota', $loker->kuota) }}">
                        </div>
                    </div>

                    <div class="mb-10">
                        <label class="required form-label">Deskripsi Pekerjaan & Persyaratan</label>
                        <div id="editor-container">
                            <textarea name="deskripsi" id="deskripsi-editor" class="form-control d-none">{{ old('deskripsi', $loker->deskripsi) }}</textarea>
                        </div>
                        @error('deskripsi') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('admin.perusahaan.event.my-detail', encrypt($loker->idregister)) }}" class="btn btn-light me-3">Batal</a>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    $(document).ready(function() {
        let editor;

        ClassicEditor
            .create(document.querySelector('#deskripsi-editor'), {
                toolbar: [
                    'heading', '|', 
                    'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
                    'undo', 'redo'
                ],
                placeholder: 'Tuliskan deskripsi pekerjaan, tanggung jawab, dan persyaratan di sini...'
            })
            .then(newEditor => {
                editor = newEditor;
            })
            .catch(error => {
                console.error(error);
            });

        // Form Progress Feedback
        $('#loker-form').on('submit', function() {
            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).addClass('btn-progress');
            if (typeof NProgress !== "undefined") NProgress.start();
        });

        // Rupiah Masking
        $('.rupiah-mask').on('input', function() {
            let val = $(this).val().replace(/[^0-9]/g, '');
            if (val) {
                val = parseInt(val).toLocaleString('id-ID');
                $(this).val(val);
            } else {
                $(this).val('');
            }
        });
    });
</script>
@endpush

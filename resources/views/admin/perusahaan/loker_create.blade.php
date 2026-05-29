@extends('layouts.admin')

@section('title', 'Tambah Lowongan - ' . $registration->even->namaperiode)

@section('content')
<div class="row g-7">
    <!-- Left Column: Manual Form -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800 fs-3">Publish Lowongan Baru</span>
                    <span class="text-muted fw-semibold fs-7 px-1">Isi detail posisi pekerjaan yang Anda cari</span>
                </h3>
            </div>
            <form id="loker-form" action="{{ route('admin.perusahaan.loker.store', encrypt($registration->id)) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-10">
                        <label class="required form-label">Nama Posisi / Lowongan</label>
                        <input type="text" name="namalowongan" class="form-control @error('namalowongan') is-invalid @enderror" placeholder="Contoh: Senior Backend Engineer" value="{{ old('namalowongan') }}" required>
                        @error('namalowongan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-10">
                        <div class="col-md-6">
                            <label class="required form-label">Kategori Pekerjaan</label>
                            <select name="idkategorilowongan" class="form-select @error('idkategorilowongan') is-invalid @enderror" data-control="select2" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('idkategorilowongan') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="required form-label">Penempatan Kerja</label>
                            <select name="kategorilokasi" class="form-select @error('kategorilokasi') is-invalid @enderror" required>
                                <option value="">Pilih Penempatan</option>
                                <option value="Dalam Negeri" {{ old('kategorilokasi') == 'Dalam Negeri' ? 'selected' : '' }}>Dalam Negeri (Domestic)</option>
                                <option value="Luar Negeri" {{ old('kategorilokasi') == 'Luar Negeri' ? 'selected' : '' }}>Luar Negeri (Overseas)</option>
                            </select>
                            @error('kategorilokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-10">
                        <div class="col-md-4">
                            <label class="form-label">Gaji Minimal (Rupiah)</label>
                            <input type="text" name="gaji_awal" class="form-control rupiah-mask" placeholder="Contoh: 5.000.000" value="{{ old('gaji_awal') }}">
                            <div class="fs-9 text-muted mt-1">Kosongkan jika dirahasiakan</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gaji Maksimal (Rupiah)</label>
                            <input type="text" name="gaji_akhir" class="form-control rupiah-mask" placeholder="Contoh: 15.000.000" value="{{ old('gaji_akhir') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kuota (Jumlah Rekrutmen)</label>
                            <input type="number" name="kuota" class="form-control" placeholder="Kosongkan jika tak terbatas" value="{{ old('kuota') }}">
                        </div>
                    </div>

                    <div class="mb-10">
                        <label class="required form-label">Deskripsi Pekerjaan & Persyaratan</label>
                        <div id="editor-container">
                            <textarea name="deskripsi" id="deskripsi-editor" class="form-control d-none">{{ old('deskripsi') }}</textarea>
                        </div>
                        @error('deskripsi') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('admin.perusahaan.event.my-detail', encrypt($registration->id)) }}" class="btn btn-light me-3">Batal</a>
                    <button type="submit" class="btn btn-primary fw-bold">Publish Lowongan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Import Metadata -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-7">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800 fs-4">Import dari Event Lain</span>
                    <span class="text-muted fw-semibold fs-7 px-1">Gunakan template loker sebelumnya</span>
                </h3>
            </div>
            <div class="card-body">
                @if($pastLowongans->count() > 0)
                    <p class="text-gray-600 fs-7 mb-6">Pilih lowongan dari event sebelumnya untuk disalin ke event ini secara massal.</p>
                    <form class="import-form" action="{{ route('admin.perusahaan.loker.import', encrypt($registration->id)) }}" method="POST">
                        @csrf
                        <div class="scroll-y mh-400px px-2">
                            @foreach($pastLowongans as $past)
                                <div class="d-flex align-items-center mb-5 p-3 rounded border border-dashed border-gray-300">
                                    <div class="form-check form-check-custom form-check-solid me-4">
                                        <input class="form-check-input" type="checkbox" name="selected_lokers[]" value="{{ $past->id }}" id="loker-{{ $past->id }}">
                                    </div>
                                    <label class="d-flex flex-column cursor-pointer" for="loker-{{ $past->id }}">
                                        <span class="text-gray-800 fw-bold fs-6">{{ $past->namalowongan }}</span>
                                        <span class="text-muted fs-8">{{ $past->kategori->nama ?? 'Umum' }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-light-info fw-bold w-100 mt-5">Import yang Dipilih</button>
                    </form>
                @else
                    <div class="text-center py-10 opacity-50">
                        <i class="material-icons fs-3tx mb-3">history</i>
                        <p class="fw-bold mb-0">Tidak Ada Riwayat</p>
                        <span class="fs-8">Belum ada lowongan dari event sebelumnya.</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="card card-flush shadow-sm border-0 bg-light-primary">
            <div class="card-body p-7">
                <h4 class="fw-bold text-primary mb-3">Event: {{ $registration->even->namaperiode }}</h4>
                <div class="text-gray-700 fs-7">
                    Lowongan yang Anda publikasikan akan tampil secara eksklusif bagi pencari kerja yang terdaftar di event ini.
                </div>
            </div>
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
        $('#loker-form, .import-form').on('submit', function(e) {
            // Synchronize CKEditor data to teaxtarea before submit
            if (editor) {
                const data = editor.getData();
                if (!data || data.trim() === '') {
                    // Optional: Validation check
                }
            }

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

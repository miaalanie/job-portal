@extends('layouts.admin')

@section('title', 'Lengkapi Profil Perusahaan')
@section('page_title', 'Kelola Profil & Dokumen Legalitas')

@section('content')
<div class="row g-5 g-xl-10">
    <div class="col-xl-4">
        <div class="card card-flush h-lg-100">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800">Status Kelengkapan</span>
                    <span class="text-gray-400 mt-1 fw-semibold fs-6">Pastikan semua data legal valid</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column align-items-center mb-10">
                    <div class="symbol symbol-100px symbol-circle mb-5 border border-secondary p-1 overflow-hidden">
                        @if($perusahaan && $perusahaan->logo)
                            <img src="{{ asset('storage/' . $perusahaan->logo) }}" alt="Logo" class="w-100 h-100 object-fit-cover">
                        @else
                            <div class="symbol-label fs-1 fw-bold bg-light-primary text-primary">
                                {{ substr($perusahaan->nama, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="fw-bold fs-3 text-gray-800 text-center">{{ $perusahaan->nama }}</div>
                    <div class="text-muted fw-semibold mb-3">{{ $perusahaan->email }}</div>
                    
                    @if($perusahaan->is_verified)
                        <span class="badge badge-light-success px-4 py-2 fs-7 fw-bold">
                            <i class="material-icons fs-7 me-1 text-success">verified</i> Terverifikasi
                        </span>
                    @else
                        <span class="badge badge-light-warning px-4 py-2 fs-7 fw-bold">
                            <i class="material-icons fs-7 me-1 text-warning">hourglass_empty</i> Menunggu Verifikasi
                        </span>
                    @endif
                </div>

                <div class="separator separator-dashed my-5"></div>
                
                <h5 class="fw-bold text-gray-800 mb-4">Ringkasan Dokumen</h5>
                <div id="existing_docs_list">
                    @forelse($perusahaan->dokumen as $dok)
                        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded position-relative group">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-white">
                                    <i class="material-icons text-primary fs-3">description</i>
                                </span>
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <a href="{{ asset('storage/' . $dok->file_path) }}" target="_blank" class="text-gray-800 fw-bold text-hover-primary fs-7">{{ $dok->nama_dokumen }}</a>
                                <span class="text-muted fs-8">{{ \Carbon\Carbon::parse($dok->created_at)->format('d M Y') }}</span>
                            </div>
                            <button type="button" class="btn btn-icon btn-sm btn-light-danger delete-doc-btn" data-id="{{ \Illuminate\Support\Facades\Crypt::encrypt($dok->id) }}" title="Hapus Dokumen">
                                <i class="material-icons fs-6">delete</i>
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-5 opacity-50 no-docs-msg">
                            <p class="small mb-0">Belum ada dokumen legalitas.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <!-- Progress Bar -->
        <div class="progress mb-5 d-none" style="height: 10px;" id="upload_progress_container">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" id="upload_progress_bar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        <form id="profile_update_form" action="{{ route('admin.perusahaan.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card card-flush mb-5 shadow-sm">
                <div class="card-header pt-7">
                    <h3 class="card-title fw-bold">
                        <i class="material-icons text-primary me-2">business</i> Biodata Perusahaan
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-bold mb-2">Nama Perusahaan</label>
                            <input type="text" class="form-control form-control-solid" name="nama" value="{{ old('nama', $perusahaan->nama) }}" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-bold mb-2">Email Bisnis</label>
                            <input type="email" class="form-control form-control-solid" name="email" value="{{ old('email', $perusahaan->email) }}" required />
                        </div>
                    </div>
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-bold mb-2">Telepon / HP</label>
                            <input type="text" class="form-control form-control-solid" name="telp" value="{{ old('telp', $perusahaan->telp) }}" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-bold mb-2">NPWP</label>
                            <input type="text" class="form-control form-control-solid" name="npwp" value="{{ old('npwp', $perusahaan->npwp) }}" />
                        </div>
                    </div>
                    <div class="fv-row mb-8">
                        <label class="required fs-6 fw-bold mb-2">Alamat Lengkap Kantor Pusat</label>
                        <textarea class="form-control form-control-solid" rows="3" name="alamatlengkap" required>{{ old('alamatlengkap', $perusahaan->alamatlengkap) }}</textarea>
                    </div>
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-bold mb-2">Tahun Berdiri</label>
                            <input type="number" class="form-control form-control-solid" name="tahunberdiri" value="{{ old('tahunberdiri', $perusahaan->tahunberdiri) }}" placeholder="Contoh: 2010" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-bold mb-2">Website Perusahaan</label>
                            <input type="url" class="form-control form-control-solid" name="website" value="{{ old('website', $perusahaan->website) }}" placeholder="https://example.com" />
                        </div>
                    </div>
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-bold mb-2">Gambaran Umum / Profil Singkat</label>
                        <textarea class="form-control form-control-solid" rows="4" name="gambaranumum" placeholder="Ceritakan singkat tentang perusahaan Anda...">{{ old('gambaranumum', $perusahaan->gambaranumum) }}</textarea>
                    </div>
                    <div class="fv-row mb-0">
                        <label class="fs-6 fw-bold mb-2">Logo Perusahaan</label>
                        <input type="file" class="form-control form-control-solid" name="logo" accept="image/*" />
                        <div class="text-muted fs-7 mt-2">Format: PNG, JPG, JPEG (Max 2MB). Kosongkan jika tidak ingin mengubah.</div>
                    </div>
                </div>
            </div>

            <div class="card card-flush shadow-sm">
                <div class="card-header pt-7">
                    <h3 class="card-title fw-bold">
                        <i class="material-icons text-primary me-2">verified_user</i> Upload Dokumen Legalitas Baru
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm btn-light-primary hover-scale" id="add_doc_row">
                            <i class="material-icons fs-5 me-1">add</i> Tambah Baris
                        </button>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                        <i class="material-icons fs-2tx text-primary me-4">info_outline</i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Panduan Dokumen</h4>
                                <div class="fs-6 text-gray-700">Lampirkan file scan NIB, SIUP, atau Surat Domisili dalam format PDF atau Gambar (Max 5MB per file).</div>
                            </div>
                        </div>
                    </div>

                    <div id="document_rows">
                        <div class="doc-row mb-5 border p-5 rounded position-relative bg-gray-50 border-dashed">
                            <div class="row g-9">
                                <div class="col-md-5">
                                    <label class="required fs-7 fw-bold mb-1">Nama Dokumen</label>
                                    <input type="text" name="dokument_names[]" class="form-control form-control-sm" placeholder="Contoh: SIUP / NIB" />
                                </div>
                                <div class="col-md-7">
                                    <label class="required fs-7 fw-bold mb-1">File Dokumen</label>
                                    <input type="file" name="dokument_files[]" class="form-control form-control-sm" accept=".pdf,image/*" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end py-6 px-9 bg-light-faint">
                    <button type="submit" class="btn btn-primary px-10 fw-bold" id="submit_form_btn">
                        <span class="indicator-label">Simpan & Perbarui Profil</span>
                        <span class="indicator-progress d-none">Mohon Tunggu... 
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<!-- PNotify -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.brighttheme.css" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.buttons.js"></script>
<script>
    $(document).ready(function() {
        // Add row functionality
        $('#add_doc_row').on('click', function() {
            const wrapper = $('#document_rows');
            const newRow = $(`
                <div class="doc-row mb-5 border p-5 rounded position-relative bg-gray-50 border-dashed">
                    <div class="row g-9">
                        <div class="col-md-5">
                            <label class="required fs-7 fw-bold mb-1">Nama Dokumen</label>
                            <input type="text" name="dokument_names[]" class="form-control form-control-sm" placeholder="Contoh: Akta Pendirian" />
                        </div>
                        <div class="col-md-6">
                            <label class="required fs-7 fw-bold mb-1">File Dokumen</label>
                            <input type="file" name="dokument_files[]" class="form-control form-control-sm" accept=".pdf,image/*" />
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-icon btn-sm btn-light-danger remove-row w-100 h-35px">
                                <i class="material-icons fs-5">delete</i>
                            </button>
                        </div>
                    </div>
                </div>
            `);
            wrapper.append(newRow);
            newRow.find('.remove-row').on('click', function() {
                newRow.slideUp(200, function() { $(this).remove(); });
            });
        });
        // Delete existing document
        $('.delete-doc-btn').on('click', function() {
            const btn = $(this);
            const id = btn.data('id');
            const item = btn.closest('.group');

            Swal.fire({
                title: 'Hapus Dokumen?',
                text: "Dokumen yang dihapus tidak dapat dipulihkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('/admin/perusahaan/profile/document') }}/${id}`,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (res.status === 'success') {
                                item.fadeOut(300, function() { 
                                    $(this).remove(); 
                                    if ($('#existing_docs_list .group').length === 0) {
                                        $('#existing_docs_list').append('<div class="text-center py-5 opacity-50 no-docs-msg"><p class="small mb-0">Belum ada dokumen legalitas.</p></div>');
                                    }
                                });
                                new PNotify({
                                    title: 'Terhapus',
                                    text: res.message,
                                    type: 'success',
                                    styling: 'brighttheme'
                                });
                            }
                        },
                        error: function() { 
                            new PNotify({
                                title: 'Gagal',
                                text: 'Gagal menghapus dokumen.',
                                type: 'error',
                                styling: 'brighttheme'
                            });
                        }
                    });
                }
            });
        });

        // AJAX Form Submission with Progress Bar
        $('#profile_update_form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const btn = $('#submit_form_btn');
            const btnLabel = btn.find('.indicator-label');
            const btnProgress = btn.find('.indicator-progress');
            const progressContainer = $('#upload_progress_container');
            const progressBar = $('#upload_progress_bar');

            const formData = new FormData(this);

            btn.prop('disabled', true);
            btnLabel.addClass('d-none');
            btnProgress.removeClass('d-none');
            progressContainer.removeClass('d-none');
            progressBar.css('width', '0%').attr('aria-valuenow', 0).text('0%');
            NProgress.start();

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            progressBar.css('width', percentComplete + '%').attr('aria-valuenow', percentComplete).text(percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(res) {
                    NProgress.done();
                    if (res.status === 'success') {
                        new PNotify({
                            title: 'Sukses',
                            text: res.message,
                            type: 'success',
                            styling: 'brighttheme',
                            delay: 2000
                        });
                        setTimeout(() => { location.reload(); }, 1500);
                    }
                },
                error: function(xhr) {
                    NProgress.done();
                    btn.prop('disabled', false);
                    btnLabel.removeClass('d-none');
                    btnProgress.addClass('d-none');
                    progressContainer.addClass('d-none');
                    
                    let msg = 'Gagal memperbarui profil.';
                    if (xhr.status === 422) msg = Object.values(xhr.responseJSON.errors)[0][0];
                    else if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    
                    new PNotify({
                        title: 'Error',
                        text: msg,
                        type: 'error',
                        styling: 'brighttheme'
                    });
                }
            });
        });
    });
</script>
@endpush

<style>
    .delete-doc-btn { opacity: 0; transition: opacity 0.2s ease; }
    .group:hover .delete-doc-btn { opacity: 1; }
    .bg-light-faint { background-color: #fcfcfc; }
    .object-fit-cover { object-fit: cover; }
    .hover-scale:hover { transform: scale(1.02); transition: transform 0.2s ease; }
</style>

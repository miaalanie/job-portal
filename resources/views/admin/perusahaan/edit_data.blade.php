@extends('layouts.admin')

@section('title', 'Edit Profil Perusahaan')
@section('page_title', 'Kelola Profil & Dokumen: ' . $perusahaan->nama)

@section('content')
<div class="row g-5 g-xl-10">
    <div class="col-xl-4">
        <div class="card card-flush h-lg-100 shadow-sm border-0">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800">Status Kelengkapan</span>
                    <span class="text-gray-400 mt-1 fw-semibold fs-6">Data audit mitra perusahaan</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column align-items-center mb-10">
                    <div class="symbol symbol-100px symbol-circle mb-5 border border-secondary p-1 overflow-hidden">
                        @if($perusahaan->logo && $perusahaan->logo != 'no-image')
                            <img src="{{ asset('storage/' . $perusahaan->logo) }}" alt="Logo" class="w-100 h-100 object-fit-cover">
                        @else
                            <div class="symbol-label fs-1 fw-bold bg-light-primary text-primary">
                                {{ substr($perusahaan->nama, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="fw-bold fs-3 text-gray-800 text-center">{{ $perusahaan->nama }}</div>
                    <div class="text-muted fw-semibold mb-3">{{ $perusahaan->email }}</div>
                    
                    @if($perusahaan->user && $perusahaan->user->statusvalidasi == 1)
                        <span class="badge badge-light-success px-4 py-2 fs-7 fw-bold">
                            <i class="material-icons fs-7 me-1 text-success">verified</i> Terverifikasi
                        </span>
                    @else
                        <span class="badge badge-light-warning px-4 py-2 fs-7 fw-bold">
                            <i class="material-icons fs-7 me-1 text-warning">hourglass_empty</i> Menunggu Audit
                        </span>
                    @endif
                </div>

                <div class="separator separator-dashed my-5"></div>
                
                <h5 class="fw-bold text-gray-800 mb-4">Dokumen Legalitas Tersedia</h5>
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
        <form id="profile_update_form" action="{{ route('admin.perusahaan-data.update', encrypt($perusahaan->id)) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card card-flush mb-5 shadow-sm border-0">
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
                            <label class="required fs-6 fw-bold mb-2">Email Akun (Login)</label>
                            <input type="email" class="form-control form-control-solid" name="email" value="{{ old('email', $perusahaan->email) }}" required />
                        </div>
                    </div>
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-bold mb-2">Kategori Industri</label>
                            <select class="form-select form-select-solid" name="idkategori" required>
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id }}" {{ $perusahaan->idkategori == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-bold mb-2">Telepon / HP</label>
                            <input type="text" class="form-control form-control-solid" name="telp" value="{{ old('telp', $perusahaan->telp) }}" required />
                        </div>
                    </div>
                    <div class="row g-9 mb-8">
                         <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-bold mb-2">NPWP</label>
                            <input type="text" class="form-control form-control-solid" name="npwp" value="{{ old('npwp', $perusahaan->npwp) }}" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-bold mb-2">NIB</label>
                            <input type="text" class="form-control form-control-solid" name="nib" value="{{ old('nib', $perusahaan->nib) }}" />
                        </div>
                    </div>
                    <div class="fv-row mb-8">
                        <label class="required fs-6 fw-bold mb-2">Alamat Lengkap Kantor Pusat</label>
                        <textarea class="form-control form-control-solid" rows="3" name="alamatlengkap" required>{{ old('alamatlengkap', $perusahaan->alamatlengkap) }}</textarea>
                    </div>
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-bold mb-2">Nama Pimpinan / CEO</label>
                            <input type="text" class="form-control form-control-solid" name="namapimpinan" value="{{ old('namapimpinan', $perusahaan->namapimpinan) }}" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-bold mb-2">Nama PIC (Kontak Person)</label>
                            <input type="text" class="form-control form-control-solid" name="pic" value="{{ old('pic', $perusahaan->pic) }}" />
                        </div>
                    </div>
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-bold mb-2">Tahun Berdiri</label>
                            <input type="number" class="form-control form-control-solid" name="tahunberdiri" value="{{ old('tahunberdiri', $perusahaan->tahunberdiri) }}" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-bold mb-2">Jumlah Karyawan</label>
                            <input type="text" class="form-control form-control-solid" name="jumlah_karyawan" value="{{ old('jumlah_karyawan', $perusahaan->jumlah_karyawan) }}" />
                        </div>
                    </div>
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-bold mb-2">Website Perusahaan</label>
                        <input type="url" class="form-control form-control-solid" name="website" value="{{ old('website', $perusahaan->website) }}" />
                    </div>
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-bold mb-2">Gambaran Umum Perusahaan</label>
                        <textarea class="form-control form-control-solid" rows="4" name="gambaranumum">{{ old('gambaranumum', $perusahaan->gambaranumum) }}</textarea>
                    </div>
                    <div class="fv-row mb-0">
                        <label class="fs-6 fw-bold mb-2">Ubah Logo Perusahaan</label>
                        <input type="file" class="form-control form-control-solid" name="logo" accept="image/*" />
                    </div>
                </div>
            </div>

            <div class="card card-flush shadow-sm border-0">
                <div class="card-header pt-7">
                    <h3 class="card-title fw-bold">
                        <i class="material-icons text-primary me-2">verified_user</i> Update Dokumen Legalitas
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm btn-light-primary" id="add_doc_row">
                            <i class="material-icons fs-5 me-1">add</i> Tambah Baris Dokumen
                        </button>
                    </div>
                </div>
                <div class="card-body pt-0">
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
                <div class="card-footer d-flex justify-content-between py-6 px-9 bg-light-faint">
                    <a href="{{ route('admin.perusahaan-data.index') }}" class="btn btn-light me-3">Kembali</a>
                    <button type="submit" class="btn btn-primary px-10 fw-bold">
                        Simpan Perubahan Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#add_doc_row').on('click', function() {
            const wrapper = $('#document_rows');
            const newRow = $(`
                <div class="doc-row mb-5 border p-5 rounded position-relative bg-gray-50 border-dashed">
                    <div class="row g-9">
                        <div class="col-md-5">
                            <label class="required fs-7 fw-bold mb-1">Nama Dokumen</label>
                            <input type="text" name="dokument_names[]" class="form-control form-control-sm" />
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
                newRow.remove();
            });
        });

        $('.delete-doc-btn').on('click', function() {
            const btn = $(this);
            const id = btn.data('id');
            const item = btn.closest('.group');

            if (confirm('Hapus dokumen ini?')) {
                $.ajax({
                    url: `{{ url('/admin/perusahaan/profile/document') }}/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        item.fadeOut(300, function() { $(this).remove(); });
                    }
                });
            }
        });
    });
</script>
@endpush

<style>
    .delete-doc-btn { opacity: 0; transition: opacity 0.2s ease; }
    .group:hover .delete-doc-btn { opacity: 1; }
    .bg-light-faint { background-color: #fcfcfc; }
    .object-fit-cover { object-fit: cover; }
</style>

@extends('layouts.admin')

@section('title', 'Kategori Perusahaan')
@section('page_title', 'Master Kategori Perusahaan')

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
@endpush

@section('content')
<div class="row g-7">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="material-icons position-absolute ms-6">search</i>
                        <input type="text" data-kt-category-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Kategori..." />
                    </div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#kt_modal_add_category">
                        <i class="material-icons fs-5 me-2">add</i> Tambah Kategori
                    </button>
                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_categories">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-50px">No</th>
                            <th class="min-w-250px">Nama Kategori</th>
                            <th class="min-w-150px text-center">Jumlah Perusahaan</th>
                            <th class="text-end min-w-100px pe-5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @foreach($categories as $index => $cat)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="text-gray-800 fw-bold fs-6">{{ $cat->nama }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-primary fs-7 fw-bold px-4 py-2">
                                        {{ $cat->perusahaans->count() }} Perusahaan
                                    </span>
                                </td>
                                <td class="text-end pe-5">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#kt_modal_edit_category_{{ $cat->id }}" 
                                                title="Edit Kategori">
                                            <i class="material-icons fs-5">edit</i>
                                        </button>
                                        <form action="{{ route('admin.kategori-perusahaan.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" title="Hapus Permanen">
                                                <i class="material-icons fs-5">delete</i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="kt_modal_edit_category_{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered mw-500px">
                                            <div class="modal-content rounded">
                                                <div class="modal-header pb-0 border-0 justify-content-end">
                                                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                                                        <i class="material-icons">close</i>
                                                    </div>
                                                </div>
                                                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                                                    <form action="{{ route('admin.kategori-perusahaan.update', $cat->id) }}" method="POST" class="form">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="mb-13 text-center">
                                                            <h1 class="mb-3">Edit Kategori Perusahaan</h1>
                                                            <div class="text-muted fw-semibold fs-5">Perbarui nama kategori industri Anda.</div>
                                                        </div>
                                                        <div class="d-flex flex-column mb-8 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span class="required">Nama Kategori</span>
                                                            </label>
                                                            <input type="text" class="form-control form-control-solid" name="nama" value="{{ $cat->nama }}" required placeholder="Contoh: Teknologi Informasi" />
                                                        </div>
                                                        <div class="text-center">
                                                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="kt_modal_add_category" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content rounded">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="material-icons">close</i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form action="{{ route('admin.kategori-perusahaan.store') }}" method="POST" class="form">
                    @csrf
                    <div class="mb-13 text-center">
                        <h1 class="mb-3">Tambah Kategori Perusahaan</h1>
                        <div class="text-muted fw-semibold fs-5">Klasifikasikan mitra industri baru di FindTalen.</div>
                    </div>
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Nama Kategori</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" name="nama" required placeholder="Contoh: E-Commerce / Manufaktur" />
                    </div>
                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#kt_table_categories').DataTable({
            "pageLength": 10,
            "order": [],
            "language": {
                "search": "Cari Kategori:",
                "lengthMenu": "Show _MENU_ records",
                "zeroRecords": "Tidak ada data kategori"
            }
        });

        // Search Filter
        $('[data-kt-category-table-filter="search"]').on('keyup', function() {
            table.search($(this).val()).draw();
        });
    });
</script>
@endpush
@endsection

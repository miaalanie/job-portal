@extends('layouts.admin')

@section('title', 'Kelola Menu')
@section('page_title', 'Konfigurasi Menu Aplikasi')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold">Manajemen Menu & Hirarki</h3>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('admin.menu.create') }}" class="btn btn-primary btn-sm">
                    <i class="material-icons fs-5 me-1">add_circle</i> Tambah Menu
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_menus">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-150px">Nama Menu</th>
                        <th class="min-w-150px">Induk Menu</th>
                        <th class="min-w-125px">URL / URL Route</th>
                        <th class="min-w-100px text-center">Tipe</th>
                        <th class="text-end min-w-100px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @foreach($menus as $menu)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px me-3">
                                        <span class="symbol-label bg-light-primary">
                                            <i class="material-icons text-primary fs-3">{{ $menu->icon ?? 'label' }}</i>
                                        </span>
                                    </div>
                                    <span class="text-gray-800 fw-bold fs-6">{{ $menu->namamenu }}</span>
                                </div>
                            </td>
                            <td>
                                @if($menu->parent)
                                    <span class="badge badge-light-success fw-bold">{{ $menu->parent->namamenu }}</span>
                                @else
                                    <span class="text-muted fs-7 italic">Menu Utama</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fs-7">{{ $menu->alamat_url }}</span>
                                    <span class="text-muted fs-8">{{ $menu->namaroute }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($menu->submenu == 1)
                                    <span class="badge badge-light-primary fw-bold">Header</span>
                                @else
                                    <span class="badge badge-light fw-bold">Item</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.menu.edit', $menu->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Edit">
                                    <i class="material-icons fs-5">edit</i>
                                </a>
                                <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus menu ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" title="Hapus">
                                        <i class="material-icons fs-5">delete</i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
<style>
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 20px;
    }
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 10px;
        display: inline-block;
        width: auto;
        border-radius: 6px;
        border: 1px solid #e1e3ea;
        padding: 6px 12px;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#kt_table_menus')) {
            $('#kt_table_menus').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "language": {
                    "search": "Cari Menu:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ - _END_ dari _TOTAL_ menu",
                    "infoEmpty": "Menampilkan 0 - 0 dari 0 menu",
                    "infoFiltered": "(disaring dari _MAX_ total menu)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    },
                    "zeroRecords": "Tidak ada data yang cocok ditemukan"
                }
            });
        }
    });
</script>
@endpush
@endsection

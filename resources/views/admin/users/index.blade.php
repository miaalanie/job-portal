@extends('layouts.admin')

@section('title', 'Kelola Pengguna')
@section('page_title', 'Daftar Pengguna')

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
<style>
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 20px;
    }
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 10px;
        display: inline-block;
        width: 300px;
        border-radius: 6px;
        border: 1px solid #e1e3ea;
        padding: 10px 15px;
    }
    .user-card-grid {
        display: none; /* Hide standard grid when DataTable is active or adapt it */
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div class="card shadow-sm mb-5">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center">
                    <span class="bg-light-primary p-3 rounded-circle me-3 d-flex align-items-center justify-content-center">
                        <i class="material-icons text-primary fs-2">group</i>
                    </span>
                    <h3 class="fw-bold mb-0">Manajemen Pengguna</h3>
                </div>
            </div>
            <div class="card-toolbar gap-3">
                <div class="d-flex align-items-center position-relative my-1">
                    <select id="role-filter" class="form-select form-select-solid fw-bold w-200px" data-placeholder="Filter Role">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm btn-flex btn-center">
                    <i class="material-icons fs-5 me-1">person_add</i> Tambah Pengguna
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-200px"><i class="material-icons fs-8 me-1 align-middle">person</i> Pengguna</th>
                            <th class="min-w-150px"><i class="material-icons fs-8 me-1 align-middle">verified_user</i> Role</th>
                            <th class="min-w-125px"><i class="material-icons fs-8 me-1 align-middle">event</i> Assign Event</th>
                            <th class="min-w-125px"><i class="material-icons fs-8 me-1 align-middle">calendar_today</i> Tgl Bergabung</th>
                            <th class="min-w-125px text-center"><i class="material-icons fs-8 me-1 align-middle">toggle_on</i> Status</th>
                            <th class="text-end min-w-100px"><i class="material-icons fs-8 me-1 align-middle">settings</i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
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
        var table = $('#kt_table_users').DataTable({
            "ajax": {
                "url": "{{ route('admin.users.index') }}",
                "type": "GET",
                "data": function(d) {
                    d.role = $('#role-filter').val();
                },
                "dataSrc": "data"
            },
            "columns": [
                {
                    "data": null,
                    "render": function(data) {
                        var img = data.gambar === 'no-image' 
                            ? 'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/300-1.jpg' 
                            : '/storage/' + data.gambar;
                        return `
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                    <div class="symbol-label">
                                        <img src="${img}" alt="${data.name}" class="w-100" />
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold fs-6">${data.name}</span>
                                    <span class="text-muted fs-7">${data.email}</span>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    "data": "roles",
                    "render": function(data) {
                        return data.map(role => `<span class="badge badge-light-primary fw-bold">${role}</span>`).join(' ');
                    }
                },
                {
                    "data": "even_name",
                    "render": function(data) {
                        return data != "-" ? `<span class="badge badge-light-success fw-bold">${data}</span>` : "-";
                    }
                },
                { "data": "created_at" },
                {
                    "data": "statusaktif",
                    "className": "text-center",
                    "render": function(data) {
                        var color = data == 1 ? 'success' : 'danger';
                        var text = data == 1 ? 'Aktif' : 'Non-Aktif';
                        return `<span class="badge badge-light-${color} fw-bold">${text}</span>`;
                    }
                },
                {
                    "data": null,
                    "className": "text-end",
                    "render": function(data) {
                        var toggleIcon = data.statusaktif == 1 ? 'block' : 'check_circle';
                        var toggleTitle = data.statusaktif == 1 ? 'Nonaktifkan' : 'Aktifkan';
                        var toggleUrl = "{{ route('admin.users.toggle-status', ':id') }}".replace(':id', data.encrypted_id);
                        var resetUrl = "{{ route('admin.users.reset', ':id') }}".replace(':id', data.encrypted_id);
                        var editUrl = "{{ route('admin.users.edit', ':id') }}".replace(':id', data.encrypted_id);
                        var deleteUrl = "{{ route('admin.users.destroy', ':id') }}".replace(':id', data.encrypted_id);
                        
                        return `
                            <div class="d-flex justify-content-end action-buttons">
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-2 btn-toggle-status" 
                                    data-url="${toggleUrl}" title="${toggleTitle}">
                                    <i class="material-icons fs-5">${toggleIcon}</i>
                                </button>
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm me-2 btn-reset-password" 
                                    data-url="${resetUrl}" title="Reset Password">
                                    <i class="material-icons fs-5">lock_reset</i>
                                </button>
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm me-2 btn-delete-user" 
                                    data-url="${deleteUrl}" title="Hapus Pengguna">
                                    <i class="material-icons fs-5 text-danger">delete</i>
                                </button>
                                <a href="${editUrl}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" title="Edit">
                                    <i class="material-icons fs-5 text-primary">edit</i>
                                </a>
                            </div>
                        `;
                    }
                }
            ],
            "pageLength": 10,
            "language": {
                "search": "Cari Pengguna:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                "paginate": {
                    "previous": "Sebelumnya",
                    "next": "Berikutnya"
                }
            }
        });

        // Filter Change Handler
        $('#role-filter').on('change', function() {
            table.ajax.reload();
        });

        // AJAX Handle Toggle Status
        $('#kt_table_users').on('click', '.btn-toggle-status', function() {
            var url = $(this).data('url');
            NProgress.start();
            $.ajax({
                url: url,
                type: 'POST',
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    NProgress.done();
                    new PNotify({
                        title: 'Sukses!',
                        text: response.message,
                        type: 'success',
                        styling: 'brighttheme',
                        delay: 2000
                    });
                    table.ajax.reload(null, false); // Reload without resetting paging
                },
                error: function() {
                    NProgress.done();
                    new PNotify({
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan sistem.',
                        type: 'error',
                        styling: 'brighttheme'
                    });
                }
            });
        });

        // AJAX Handle Reset Password
        $('#kt_table_users').on('click', '.btn-reset-password', function() {
            var url = $(this).data('url');
            Swal.fire({
                title: 'Reset Password?',
                text: "Password user ini akan direset ke default 'password'.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(response) {
                            NProgress.done();
                            new PNotify({
                                title: 'Sukses!',
                                text: response.message,
                                type: 'success',
                                styling: 'brighttheme',
                                delay: 2000
                            });
                            table.ajax.reload(null, false);
                        },
                        error: function() {
                            NProgress.done();
                        }
                    });
                }
            });
        });

        // AJAX Handle Delete User
        $('#kt_table_users').on('click', '.btn-delete-user', function() {
            var url = $(this).data('url');
            Swal.fire({
                title: 'Hapus Pengguna?',
                text: "Hati-hati: Menghapus pengguna bersifat permanen dan tidak dapat dipulihkan!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#f1416c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Permanen!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(response) {
                            NProgress.done();
                            new PNotify({
                                title: 'Dihapus!',
                                text: response.message,
                                type: 'success',
                                styling: 'brighttheme',
                                delay: 2000
                            });
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            NProgress.done();
                            let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal menghapus data.';
                            new PNotify({
                                title: 'Error',
                                text: msg,
                                type: 'error',
                                styling: 'brighttheme'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection

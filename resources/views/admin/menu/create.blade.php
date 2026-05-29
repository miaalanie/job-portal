@extends('layouts.admin')

@section('title', 'Tambah Menu')
@section('page_title', 'Tambah Menu/Submenu Baru')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">Form Input Menu</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('admin.menu') }}" class="btn btn-light-primary btn-sm btn-flex btn-center">
                <i class="material-icons fs-5 me-1">arrow_back</i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.menu.store') }}" method="POST">
            @csrf
            
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-bold fs-6">Nama Menu</label>
                <div class="col-lg-8 fv-row">
                    <input type="text" name="namamenu" class="form-control form-control-lg form-control-solid" placeholder="Contoh: Kelola User" required />
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-bold fs-6">URL / Alamat URL</label>
                <div class="col-lg-8 fv-row">
                    <input type="text" name="alamat_url" class="form-control form-control-lg form-control-solid" placeholder="Contoh: /admin/users atau #" required />
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-bold fs-6">Nama Route</label>
                <div class="col-lg-8 fv-row">
                    <input type="text" name="namaroute" class="form-control form-control-lg form-control-solid" placeholder="Contoh: admin.users.index" />
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-bold fs-6">Ikon (Material Icon)</label>
                <div class="col-lg-8 fv-row position-relative">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-40px me-4 border border-gray-300 border-dashed rounded p-1 bg-light">
                            <span class="symbol-label bg-transparent">
                                <i class="material-icons text-primary fs-2" id="icon-preview">label</i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <input type="text" id="icon-input" name="icon" class="form-control form-control-lg form-control-solid" placeholder="Ketik nama ikon (misal: person, settings...)" list="icon-list" autocomplete="off" />
                            <datalist id="icon-list">
                                <option value="dashboard">Dashboard</option>
                                <option value="person">Person</option>
                                <option value="group">Group</option>
                                <option value="settings">Settings</option>
                                <option value="description">Description</option>
                                <option value="assessment">Assessment</option>
                                <option value="account_balance">Bank</option>
                                <option value="store">Store</option>
                                <option value="storage">Database</option>
                                <option value="list">List</option>
                                <option value="history">History</option>
                                <option value="notifications">Notifications</option>
                                <option value="mail">Mail</option>
                                <option value="search">Search</option>
                                <option value="lock">Lock</option>
                                <option value="security">Security</option>
                                <option value="manage_accounts">Manage Accounts</option>
                                <option value="checklist">Checklist</option>
                                <option value="category">Category</option>
                                <option value="layers">Layers</option>
                                <option value="grid_view">Grid View</option>
                                <option value="widgets">Widgets</option>
                                <option value="event">Event</option>
                                <option value="calendar_today">Calendar</option>
                            </datalist>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-bold fs-6">Memiliki Submenu?</label>
                <div class="col-lg-8 d-flex align-items-center">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-30px w-50px" type="checkbox" value="1" id="submenu-toggle" name="submenu" onchange="toggleParentSelection(this.checked)" />
                        <label class="form-check-label fw-bold text-gray-700 ms-3" for="submenu-toggle">
                            Aktifkan sebagai Parent / Header Menu
                        </label>
                    </div>
                    <input type="hidden" name="submenu" id="submenu-hidden" value="0">
                </div>
            </div>

            <div class="row mb-6" id="parent_selection">
                <label class="col-lg-4 col-form-label fw-bold fs-6">Pilih Parent Menu</label>
                <div class="col-lg-8 fv-row">
                    <select name="idmenu" class="form-select form-select-lg form-select-solid">
                        <option value="0">-- Tanpa Parent --</option>
                        @foreach($parentMenus as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->namamenu }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-light me-3">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Menu</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Handle Icon Preview
    $('#icon-input').on('input change', function() {
        const iconName = $(this).val();
        if (iconName) {
            $('#icon-preview').text(iconName);
        } else {
            $('#icon-preview').text('label');
        }
    });

    // Initial state
    toggleParentSelection(false);
});

function toggleParentSelection(isChecked) {
    const parentDiv = document.getElementById('parent_selection');
    const hiddenInput = document.getElementById('submenu-hidden');
    
    if (isChecked) {
        parentDiv.style.display = 'none';
        hiddenInput.disabled = true; // Use the checkbox value
    } else {
        parentDiv.style.display = 'flex';
        hiddenInput.disabled = false; // Use the hidden 0 value
    }
}
</script>
@endpush
@endsection

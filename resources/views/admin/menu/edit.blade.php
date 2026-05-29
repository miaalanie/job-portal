@extends('layouts.admin')

@section('title', 'Edit Menu')
@section('page_title', 'Perbarui Menu/Submenu')

@section('content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">Edit Menu: {{ $menu->namamenu }}</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('admin.menu') }}" class="btn btn-light-primary btn-sm btn-flex btn-center">
                <i class="material-icons fs-5 me-1">arrow_back</i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.menu.update', $menu->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-bold fs-6">Nama Menu</label>
                <div class="col-lg-8 fv-row">
                    <input type="text" name="namamenu" class="form-control form-control-lg form-control-solid" value="{{ $menu->namamenu }}" required />
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-bold fs-6">URL / Alamat URL</label>
                <div class="col-lg-8 fv-row">
                    <input type="text" name="alamat_url" class="form-control form-control-lg form-control-solid" value="{{ $menu->alamat_url }}" required />
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-bold fs-6">Nama Route</label>
                <div class="col-lg-8 fv-row">
                    <input type="text" name="namaroute" class="form-control form-control-lg form-control-solid" value="{{ $menu->namaroute }}" />
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-bold fs-6">Ikon (Material Icon)</label>
                <div class="col-lg-8 fv-row">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-40px me-4 border border-gray-300 border-dashed rounded p-1 bg-light">
                            <span class="symbol-label bg-transparent">
                                <i class="material-icons text-primary fs-2" id="icon-preview">{{ $menu->icon ?? 'label' }}</i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <input type="text" id="icon-input" name="icon" class="form-control form-control-lg form-control-solid" value="{{ $menu->icon }}" list="icon-list" autocomplete="off" />
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
                            </datalist>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-bold fs-6">Memiliki Submenu?</label>
                <div class="col-lg-8 d-flex align-items-center">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-30px w-50px" type="checkbox" value="1" id="submenu-toggle" name="submenu" {{ $menu->submenu == 1 ? 'checked' : '' }} onchange="toggleParentSelection(this.checked)" />
                        <label class="form-check-label fw-bold text-gray-700 ms-3" for="submenu-toggle">
                            Aktifkan sebagai Parent / Header Menu
                        </label>
                    </div>
                    <input type="hidden" name="submenu" id="submenu-hidden" value="0" {{ $menu->submenu == 1 ? 'disabled' : '' }}>
                </div>
            </div>

            <div class="row mb-6" id="parent_selection" style="{{ $menu->submenu == 1 ? 'display:none' : '' }}">
                <label class="col-lg-4 col-form-label fw-bold fs-6">Pilih Parent Menu</label>
                <div class="col-lg-8 fv-row">
                    <select name="idmenu" class="form-select form-select-lg form-select-solid">
                        <option value="0">-- Tanpa Parent --</option>
                        @foreach($parentMenus as $parent)
                            <option value="{{ $parent->id }}" {{ $menu->idmenu == $parent->id ? 'selected' : '' }}>{{ $parent->namamenu }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-light me-3">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui Menu</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#icon-input').on('input change', function() {
        const iconName = $(this).val();
        if (iconName) {
            $('#icon-preview').text(iconName);
        } else {
            $('#icon-preview').text('label');
        }
    });
});

function toggleParentSelection(isChecked) {
    const parentDiv = document.getElementById('parent_selection');
    const hiddenInput = document.getElementById('submenu-hidden');
    
    if (isChecked) {
        parentDiv.style.display = 'none';
        hiddenInput.disabled = true;
    } else {
        parentDiv.style.display = 'flex';
        hiddenInput.disabled = false;
    }
}
</script>
@endpush
@endsection

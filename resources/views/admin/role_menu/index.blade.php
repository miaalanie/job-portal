@extends('layouts.admin')

@section('title', 'Hak Akses Menu')
@section('page_title', 'Manajemen Akses Menu per Role')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div class="card shadow-sm">
        <div class="card-header card-header-stretch">
            <h3 class="card-title">Pilih Role</h3>
            <div class="card-toolbar">
                <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                    @foreach($roles as $role)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }} fw-bold text-active-primary" 
                               data-bs-toggle="tab" 
                               href="#kt_tab_pane_{{ $role->id }}">
                                {{ $role->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                @foreach($roles as $role)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="kt_tab_pane_{{ $role->id }}" role="tabpanel">
                        <form action="{{ route('admin.role-menu.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="idrole" value="{{ $role->id }}">
                            
                            <div class="row g-9">
                                @foreach($menus as $menu)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card border border-dashed border-gray-300 rounded p-6 h-100">
                                            <div class="d-flex align-items-center mb-5">
                                                <div class="form-check form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input h-25px w-25px parent-check" 
                                                           type="checkbox" 
                                                           name="idmenus[]" 
                                                           value="{{ $menu->id }}" 
                                                           id="menu_{{ $role->id }}_{{ $menu->id }}"
                                                           {{ in_array($menu->id, $access[$role->id] ?? []) ? 'checked' : '' }} 
                                                           data-parent="{{ $menu->id }}"
                                                           data-role="{{ $role->id }}"/>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons text-primary fs-2 me-2">{{ $menu->icon ?? 'label' }}</i>
                                                    <label class="fs-5 fw-bold text-gray-800 cursor-pointer" for="menu_{{ $role->id }}_{{ $menu->id }}">
                                                        {{ $menu->namamenu }}
                                                    </label>
                                                </div>
                                            </div>

                                            @if($menu->subMenus->count() > 0)
                                                <div class="separator separator-dashed my-4"></div>
                                                <div class="d-flex flex-column gap-3 ps-10">
                                                    @foreach($menu->subMenus as $sub)
                                                        <div class="d-flex align-items-center">
                                                            <div class="form-check form-check-custom form-check-solid me-3">
                                                                <input class="form-check-input h-20px w-20px sub-check-{{ $role->id }}-{{ $menu->id }}" 
                                                                       type="checkbox" 
                                                                       name="idmenus[]" 
                                                                       value="{{ $sub->id }}" 
                                                                       id="menu_{{ $role->id }}_{{ $sub->id }}"
                                                                       {{ in_array($sub->id, $access[$role->id] ?? []) ? 'checked' : '' }} />
                                                            </div>
                                                            <label class="fs-6 fw-semibold text-gray-600 cursor-pointer" for="menu_{{ $role->id }}_{{ $sub->id }}">
                                                                {{ $sub->namamenu }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-end mt-10">
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">Simpan Akses {{ $role->name }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Logic to select all submenus when parent is checked
    $('.parent-check').on('change', function() {
        const parentId = $(this).data('parent');
        const roleId = $(this).data('role');
        const isChecked = $(this).prop('checked');
        
        $(`.sub-check-${roleId}-${parentId}`).prop('checked', isChecked);
    });
});
</script>
@endpush
@endsection

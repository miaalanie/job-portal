@extends('layouts.admin')

@section('title', 'Kelola Event')
@section('page_title', 'Manajemen Event Job Fair')

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
@endpush

@section('content')
@php
$user = auth()->user();
$isSuperAdmin = $user->hasRole('Superadmin');
$isAdminPerusahaan = $user->hasRole('Admin Perusahaan');
@endphp
<div class="d-flex flex-column flex-column-fluid">
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bold">Daftar Event Job Fair</h3>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('admin.event.create') }}" class="btn btn-primary btn-sm">
                    <i class="material-icons fs-5 me-1">event_available</i> Tambah Event
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_events">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-150px">Nama Periode</th>
                        <th class="min-w-150px">Pelaksanaan</th>
                        <th class="min-w-150px">Lokasi</th>
                        <th class="min-w-100px text-center">Status</th>
                        <th class="min-w-100px text-center">Approval</th>
                        <th class="text-end min-w-100px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @foreach($events as $event)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-50px me-3">
                                    @if($event->gambar)
                                    <img src="{{ asset('storage/' . $event->gambar) }}" alt="event" />
                                    @else
                                    <span class="symbol-label bg-light-primary text-primary fw-bold text-uppercase">
                                        {{ substr($event->namaperiode, 0, 1) }}
                                    </span>
                                    @endif
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold fs-6">{{ $event->namaperiode }}</span>
                                    <span class="text-muted fs-7">{{ $event->keterangan ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column text-gray-800">
                                <div class="fs-7"><i class="material-icons fs-9 text-success me-1">calendar_today</i> {{ \Carbon\Carbon::parse($event->tanggalawal)->format('d M Y') }}</div>
                                <div class="fs-7"><i class="material-icons fs-9 text-danger me-1">event_busy</i> {{ \Carbon\Carbon::parse($event->tanggalselesai)->format('d M Y') }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="text-gray-800"><i class="material-icons fs-7 text-primary me-1">place</i> {{ $event->lokasi }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center gap-2">

                                <button type="button"
                                    class="btn btn-sm btn-light-{{ $event->statusaktif ? 'success' : 'danger' }} fw-bold px-3 py-1 toggle-status"
                                    data-id="{{ encrypt($event->id) }}"
                                    data-url="{{ route('admin.event.toggle-status', encrypt($event->id)) }}">
                                    {{ $event->statusaktif ? 'Aktif' : 'Tidak Aktif' }}
                                </button>

                                {{-- Headline --}}
                                <button type="button"
                                    class="btn btn-sm btn-light-{{ $event->statusheadline ? 'warning' : 'secondary' }} fw-bold px-3 py-1 toggle-headline"
                                    data-id="{{ encrypt($event->id) }}"
                                    data-url="{{ route('admin.event.toggle-headline', encrypt($event->id)) }}">
                                    <i class="material-icons fs-9 me-1 align-middle text-{{ $event->statusheadline ? 'warning' : 'muted' }}">star</i>
                                    {{ $event->statusheadline ? 'Headline' : 'Reguler' }}
                                </button>

                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center gap-2">
                                {{-- Status Approval --}}
                                @if($event->status_approval === 'pending')
                                <span class="badge badge-light-warning fw-bold">Menunggu Persetujuan</span>
                                @elseif($event->status_approval === 'approved')
                                <span class="badge badge-light-success fw-bold">Disetujui</span>
                                @elseif($event->status_approval === 'rejected')
                                <span class="badge badge-light-danger fw-bold">Ditolak</span>
                                @endif

                                {{-- Catatan penolakan --}}
                                @if($event->status_approval === 'rejected' && $event->catatan)
                                <span class="text-muted fs-9 text-center" style="max-width: 160px;" title="{{ $event->catatan }}">
                                    <i class="material-icons fs-9 text-danger align-middle">info</i>
                                    {{ \Illuminate\Support\Str::limit($event->catatan, 40) }}
                                </span>
                                @endif

                                {{-- Approve/Reject: hanya Superadmin, hanya event pending --}}
                                @if($isSuperAdmin && $event->status_approval === 'pending')
                                <div class="d-flex gap-1">
                                    <button type="button"
                                        class="btn btn-sm btn-light-success fw-bold px-3 py-1 btn-approve-event"
                                        data-url="{{ route('admin.event.approve', encrypt($event->id)) }}"
                                        data-name="{{ $event->namaperiode }}">
                                        <i class="material-icons fs-9 align-middle">check</i> Approve
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-light-danger fw-bold px-3 py-1 btn-reject-event"
                                        data-url="{{ route('admin.event.reject', encrypt($event->id)) }}">
                                        <i class="material-icons fs-9 align-middle">close</i> Reject
                                    </button>
                                </div>
                                @endif
                            </div>
                        </td>

                        <td class="text-end">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 show-qr"
                                    data-url="{{ route('frontend.event.vacancies', encrypt($event->id)) }}"
                                    data-name="{{ $event->namaperiode }}"
                                    title="QR Code Lowongan">
                                    <i class="material-icons fs-5">qr_code_2</i>
                                </button>
                                <a href="{{ route('admin.event.show', encrypt($event->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" title="Detail Event">
                                    <i class="material-icons fs-5">visibility</i>
                                </a>
                                <a href="{{ route('admin.event.sponsor', encrypt($event->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm me-1" title="Kelola Sponsor">
                                    <i class="material-icons fs-5">loyalty</i>
                                </a>
                                <a href="{{ route('admin.register', ['idperiode' => encrypt($event->id)]) }}" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm me-1" title="Lihat Pendaftar Perusahaan">
                                    <i class="material-icons fs-5">business</i>
                                </a>
                                <a href="{{ route('admin.event.edit', encrypt($event->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Edit">
                                    <i class="material-icons fs-5">edit</i>
                                </a>
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-event" data-id="{{ encrypt($event->id) }}" title="Hapus">
                                    <i class="material-icons fs-5">delete</i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="qrModalTitle">QR Code Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-10">
                <p class="text-muted mb-6 fs-7">Bagikan QR Code ini untuk memudahkan masyarakat mengakses daftar lowongan tanpa harus login.</p>
                <div id="qr-container" class="bg-white p-5 rounded-4 border shadow-sm d-inline-block mb-7">
                    <img id="qr-image" src="" alt="QR Code" class="img-fluid" style="width: 250px; height: 250px;">
                </div>
                <div class="bg-light p-4 rounded-3 text-break mb-7">
                    <p class="fs-9 text-uppercase fw-bold text-muted mb-1 ls-1">URL Publik:</p>
                    <code id="qr-url-text" class="text-primary fw-bold fs-8"></code>
                </div>
                <div class="d-grid">
                    <button type="button" id="btn-download-qr" class="btn btn-primary py-3 rounded-pill fw-bold">
                        <i class="material-icons me-1 fs-5">download</i> Download QR Code
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-outline-dark px-5 rounded-pill fw-bold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Event Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Setujui Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-6">
                <p class="text-muted mb-0">Event <strong id="approve-event-name"></strong> akan langsung <span class="text-success fw-bold">aktif</span> setelah disetujui. Lanjutkan?</p>
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn-confirm-approve" class="btn btn-success rounded-pill fw-bold">
                    <i class="material-icons fs-9 align-middle">check</i> Ya, Setujui
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Event Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <form id="form-reject-event">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tolak Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-6">
                    <label class="form-label fw-bold">Alasan Penolakan</label>
                    <textarea id="reject-catatan" class="form-control" rows="4" required placeholder="Jelaskan alasan event ini ditolak..."></textarea>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill fw-bold">Tolak Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .ls-1 {
        letter-spacing: 0.5px;
    }
</style>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('js/admin/event.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.show-qr').on('click', function() {
            let url = $(this).data('url');
            let name = $(this).data('name');
            let qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(url)}`;

            $('#qrModalTitle').text(`QR Code: ${name}`);
            $('#qr-image').attr('src', qrUrl);
            $('#qr-url-text').text(url);
            $('#qrModal').modal('show');
        });

        $('#btn-download-qr').on('click', function() {
            let qrSrc = $('#qr-image').attr('src');
            let eventName = $('#qrModalTitle').text().replace('QR Code: ', '');

            fetch(qrSrc)
                .then(resp => resp.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = `QR-Code-Lowongan-${eventName.replace(/\s+/g, '-').toLowerCase()}.png`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                })
                .catch(() => alert('Gagal mendownload QR Code.'));
        });
    });

    let approveUrl = null;
    let rejectUrl = null;

    $('.btn-approve-event').on('click', function() {
        approveUrl = $(this).data('url');
        $('#approve-event-name').text($(this).data('name'));
        $('#approveModal').modal('show');
    });

    $('#btn-confirm-approve').on('click', function() {
        $.post(approveUrl, {
                _token: '{{ csrf_token() }}'
            })
            .done(function() {
                $('#approveModal').modal('hide');
                location.reload();
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Gagal menyetujui event.');
            });
    });

    $('.btn-reject-event').on('click', function() {
        rejectUrl = $(this).data('url');
        $('#reject-catatan').val('');
        $('#rejectModal').modal('show');
    });

    $('#form-reject-event').on('submit', function(e) {
        e.preventDefault();
        $.post(rejectUrl, {
                _token: '{{ csrf_token() }}',
                catatan: $('#reject-catatan').val()
            })
            .done(function() {
                $('#rejectModal').modal('hide');
                location.reload();
            })
            .fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Gagal menolak event.');
            });
    });
</script>
@endpush
@endsection
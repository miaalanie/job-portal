@extends('layouts.admin')

@section('title', 'Tinjau Profil Perusahaan')
@section('page_title', 'Verifikasi Dokumen Pendaftaran')

@section('content')
<div class="row g-5 g-xl-10">
    <!-- Left Side: Profile & Stats -->
    <div class="col-xl-4">
        <div class="card card-flush h-lg-100 shadow-sm border-0">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-title fw-bold text-gray-800">Review Profil</span>
                    <span class="text-gray-400 mt-1 fw-semibold fs-6 px-1">Tinjau kesesuaian data pendaftar</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column align-items-center mb-10">
                    <div class="symbol symbol-120px symbol-circle mb-5 border border-secondary p-1">
                        @if($userVisible->perusahaan && $userVisible->perusahaan->logo)
                            <img src="{{ asset('storage/' . $userVisible->perusahaan->logo) }}" alt="Logo" class="w-100 h-100 object-fit-cover shadow-sm">
                        @else
                            <div class="symbol-label fs-1 fw-bold bg-light-primary text-primary">
                                {{ substr($userVisible->perusahaan->nama ?? $userVisible->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="fw-bold fs-3 text-gray-800 text-center">{{ $userVisible->perusahaan->nama ?? 'Nama Perusahaan' }}</div>
                    <div class="text-muted fw-semibold mb-3">{{ $userVisible->perusahaan->email ?? $userVisible->email }}</div>
                    
                    <span class="badge badge-light-warning px-4 py-2 fs-7 fw-bold">
                        <i class="material-icons fs-7 me-1 text-warning">pending_actions</i> Menunggu Review Admin
                    </span>
                </div>

                <div class="separator separator-dashed my-5"></div>

                <div class="d-flex flex-column gap-5">
                    <div class="d-flex align-items-center">
                         <div class="bg-light p-2 rounded me-3"><i class="material-icons text-primary fs-4">phone</i></div>
                         <div class="d-flex flex-column">
                             <span class="text-gray-400 fs-8 fw-bold">TELEPON</span>
                             <span class="text-gray-800 fw-bold fs-7">{{ $userVisible->perusahaan->telp ?? '-' }}</span>
                         </div>
                    </div>
                    <div class="d-flex align-items-center">
                         <div class="bg-light p-2 rounded me-3"><i class="material-icons text-primary fs-4">description</i></div>
                         <div class="d-flex flex-column">
                             <span class="text-gray-400 fs-8 fw-bold">NPWP / NIB</span>
                             <span class="text-gray-800 fw-bold fs-7">{{ $userVisible->perusahaan->npwp ?? '-' }} / {{ $userVisible->perusahaan->nib ?? '-' }}</span>
                         </div>
                    </div>
                    <div class="d-flex align-items-center">
                         <div class="bg-light p-2 rounded me-3"><i class="material-icons text-primary fs-4">place</i></div>
                         <div class="d-flex flex-column">
                             <span class="text-gray-400 fs-8 fw-bold">ALAMAT</span>
                             <span class="text-gray-800 fw-bold fs-7 opacity-75">{{ $userVisible->perusahaan->alamatlengkap ?? '-' }}</span>
                         </div>
                    </div>
                    <div class="d-flex align-items-center">
                         <div class="bg-light p-2 rounded me-3"><i class="material-icons text-primary fs-4">language</i></div>
                         <div class="d-flex flex-column">
                             <span class="text-gray-400 fs-8 fw-bold">WEBSITE</span>
                             <a href="{{ $userVisible->perusahaan->website ?? '#' }}" target="_blank" class="text-primary fw-bold fs-7 text-hover-primary">{{ $userVisible->perusahaan->website ?? '-' }}</a>
                         </div>
                    </div>
                    
                    <div class="separator separator-dashed my-2"></div>
                    
                    <div class="row g-5 mt-2">
                        <div class="col-6">
                            <span class="text-gray-400 fs-8 fw-bold d-block">TAHUN BEDIRI</span>
                            <span class="text-gray-800 fw-bold fs-7">{{ $userVisible->perusahaan->tahunberdiri ?? '-' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-gray-400 fs-8 fw-bold d-block">KARYAWAN</span>
                            <span class="text-gray-800 fw-bold fs-7">{{ $userVisible->perusahaan->jumlah_karyawan ?? '-' }} Orang</span>
                        </div>
                        <div class="col-6">
                            <span class="text-gray-400 fs-8 fw-bold d-block">PIMPINAN</span>
                            <span class="text-gray-800 fw-bold fs-7">{{ $userVisible->perusahaan->namapimpinan ?? '-' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-gray-400 fs-8 fw-bold d-block">PIC PORTAL</span>
                            <span class="text-gray-800 fw-bold fs-7 text-uppercase">{{ $userVisible->perusahaan->pic ?? $userVisible->name }}</span>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-5"></div>
                    
                    <div class="bg-light-faint p-4 rounded-3">
                         <span class="text-gray-400 fs-8 fw-bold d-block mb-2">GAMBARAN UMUM</span>
                         <div class="text-gray-700 fs-7 scroll-y mh-150px">
                             {{ $userVisible->perusahaan->gambaranumum ?? 'Tidak ada deskripsi.' }}
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side: Document Gallery & Actions -->
    <div class="col-xl-8">
        <div class="card card-flush mb-5 shadow-sm border-0">
             <div class="card-header pt-7">
                <h3 class="card-title fw-bold text-gray-800">
                    <i class="material-icons text-primary me-2">verified_user</i> Dokumen Pendaftaran
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-5">
                    @forelse($userVisible->perusahaan->dokumen ?? [] as $doc)
                        <div class="col-md-6 mb-5">
                            <div class="doc-card p-5 border rounded-3 h-100 bg-light-faint">
                                <div class="d-flex align-items-center mb-4">
                                     <div class="symbol symbol-40px me-4">
                                         <span class="symbol-label bg-white border">
                                             <i class="material-icons text-primary fs-2">insert_drive_file</i>
                                         </span>
                                     </div>
                                     <div class="d-flex flex-column">
                                         <span class="text-gray-800 fw-bold fs-6">{{ $doc->nama_dokumen }}</span>
                                         <span class="text-muted fs-8">Diupload {{ $doc->created_at->format('d M Y') }}</span>
                                     </div>
                                </div>
                                <div class="text-center bg-gray-100 py-10 rounded mb-4 shadow-inner">
                                    <i class="material-icons fs-3tx text-gray-300">visibility</i>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-light-primary fw-bold w-100">Lihat File</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-10 opacity-50">
                            <i class="material-icons fs-3tx mb-3">folder_off</i>
                            <div class="fw-bold">Tidak Ada Dokumen</div>
                            <div class="text-muted">Perusahaan ini belum mengunggah dokumen legalitas.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="row g-5">
            <div class="col-md-6">
                 @if($userVisible->statusaktif == 1)
                     <div class="card bg-light-success border-success border-dashed h-100">
                         <div class="card-body p-6 text-center">
                             <h4 class="fw-bold text-success mb-2">Terima Validasi</h4>
                             <p class="text-success opacity-75 fs-7 mb-5">Akun akan divalidasi dan notifikasi email akan dikirim otomatis ke perusahaan.</p>
                             <button type="button" class="btn btn-success fw-bold px-10" id="btn-approve">Setujui Akun</button>
                         </div>
                     </div>
                 @else
                     <div class="card bg-light-danger border-danger border-dashed h-100">
                         <div class="card-body p-6 text-center">
                             <i class="material-icons text-danger fs-2hx mb-2">warning</i>
                             <h4 class="fw-bold text-danger mb-2">Aktivasi Diperlukan</h4>
                             <p class="text-danger opacity-75 fs-7 mb-5">Perusahaan belum mengaktivasi email mereka. Anda tidak dapat menyetujui akun sampai status email aktif.</p>
                             <button type="button" class="btn btn-secondary fw-bold px-10 cursor-not-allowed" disabled>Setujui Akun</button>
                         </div>
                     </div>
                 @endif
            </div>
            <div class="col-md-6">
                 <div class="card bg-light-danger border-danger border-dashed h-100">
                     <div class="card-body p-6 text-center">
                         <h4 class="fw-bold text-danger mb-2">Tolak Validasi</h4>
                         <p class="text-danger opacity-75 fs-7 mb-5">Admin harus memasukkan alasan penolakan untuk dikirimkan melalui email.</p>
                         <button type="button" class="btn btn-outline-danger fw-bold px-10" id="btn-reject-modal">Tolak Pendaftaran</button>
                     </div>
                 </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Alasan Penolakan Validasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-bold small">Alasan / Saran Perbaikan (Dikirim ke Email):</label>
                <textarea class="form-control" id="reject-reason" rows="5" placeholder="Contoh: Dokumen NIB buram atau expired, mohon upload ulang."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger fw-bold" id="btn-reject-submit">Kirim Penolakan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Approve
        $('#btn-approve').on('click', function() {
            Swal.fire({
                title: 'Konfirmasi Persetujuan?',
                text: "Memvalidasi perusahaan akan mengaktifkan seluruh fitur recruitment mereka.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00796B',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    $.ajax({
                        url: `{{ route('admin.perusahaan.approve', encrypt($userVisible->id)) }}`,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            NProgress.done();
                            new PNotify({ title: 'Berhasil', text: res.message, type: 'success', styling: 'brighttheme' });
                            setTimeout(() => { window.location.href = "{{ route('admin.dashboard') }}"; }, 1500);
                        },
                        error: function() { NProgress.done(); PNotify.error({ title: 'Gagal', text: 'Terjadi kesalahan sistem.' }); }
                    });
                }
            });
        });

        // Open Reject Modal
        $('#btn-reject-modal').on('click', function() {
            $('#modalReject').modal('show');
        });

        // Submit Reject
        $('#btn-reject-submit').on('click', function() {
            const reason = $('#reject-reason').val();
            if(!reason) {
                alert('Silakan isi alasan penolakan.');
                return;
            }

            $('#modalReject').modal('hide');
            NProgress.start();

            $.ajax({
                url: `{{ route('admin.perusahaan.reject', encrypt($userVisible->id)) }}`,
                type: 'POST',
                data: { 
                    _token: '{{ csrf_token() }}',
                    reason: reason
                },
                success: function(res) {
                    NProgress.done();
                    new PNotify({ title: 'Ditolak', text: res.message, type: 'info', styling: 'brighttheme' });
                    setTimeout(() => { window.location.href = "{{ route('admin.dashboard') }}"; }, 1500);
                },
                error: function() { NProgress.done(); PNotify.error({ title: 'Gagal', text: 'Gagal mengirim penolakan.' }); }
            });
        });
    });
</script>
@endpush

<style>
    .bg-light-faint { background-color: #f9f9f9; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.06); }
    .object-fit-cover { object-fit: cover; }
    .doc-card { border-style: dashed !important; border-width: 2px !important; }
</style>

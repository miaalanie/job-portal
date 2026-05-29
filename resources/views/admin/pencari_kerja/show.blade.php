@extends('layouts.admin')

@section('title', 'Audit Talent: ' . $applicant->namalengkap)

@section('content')
@php
    $isCompany = Auth::user()->idperusahaan != null;
    $downloadRoute = $isCompany ? route('admin.perusahaan.pelamar.download-cv', encrypt($applicant->id)) : route('admin.pencari-kerja.download-cv', encrypt($applicant->id));
    $mailAction = $isCompany ? route('admin.perusahaan.pelamar.send-mail', encrypt($applicant->id)) : route('admin.pencari-kerja.send-mail', encrypt($applicant->id));
@endphp
<div class="row g-7">
    <!-- Talent Profile Header -->
    <div class="col-12">
        <div class="card mb-5 mb-xl-10 shadow-sm border-0">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="me-7 mb-4">
                        <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative border border-4 border-light">
                            @if($applicant->foto && $applicant->foto != 'no-image')
                                <img src="{{ asset('storage/'.$applicant->foto) }}" alt="image">
                            @else
                                <span class="symbol-label fs-1 text-primary bg-light-primary fw-bold">{{ substr($applicant->namalengkap, 0, 1) }}</span>
                            @endif
                            <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-gray-900 fs-2 fw-bold me-1">{{ $applicant->namalengkap }}</span>
                                    <i class="material-icons text-primary fs-3">verified</i>
                                </div>
                                <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                    <span class="d-flex align-items-center text-gray-400 me-5 mb-2">
                                        <i class="material-icons fs-5 me-1">id_card</i> NIK: {{ $applicant->noktp ?? '-' }}
                                    </span>
                                    <span class="d-flex align-items-center text-gray-400 me-5 mb-2">
                                        <i class="material-icons fs-5 me-1">location_on</i> {{ $applicant->alamatlengkap ?? '-' }}
                                    </span>
                                    <span class="d-flex align-items-center text-gray-400 mb-2">
                                        <i class="material-icons fs-5 me-1">cake</i> {{ $applicant->tempatlahir }}, {{ \Carbon\Carbon::parse($applicant->tanggallahir)->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                             <div class="d-flex my-4">
                                 <a href="{{ url()->previous() }}" class="btn btn-sm btn-light me-2 fw-bold">
                                     <i class="material-icons fs-5 me-1">arrow_back</i> Kembali
                                 </a>
                                 <button type="button" class="btn btn-sm btn-light-primary me-2 fw-bold" data-bs-toggle="modal" data-bs-target="#modalChat">
                                     <i class="material-icons fs-5 me-1">mail</i> Chat Talent
                                 </button>
                                 <a href="{{ $downloadRoute }}" class="btn btn-sm btn-primary fw-bold">
                                     <i class="material-icons fs-5 me-1">description</i> Unduh CV (PDF)
                                 </a>
                             </div>
                        </div>
                        <div class="d-flex flex-wrap flex-stack">
                            <div class="d-flex flex-column flex-grow-1 pe-8">
                                <div class="d-flex flex-wrap">
                                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="fs-2 fw-bold">{{ $applicant->lamarans->count() }}</div>
                                        </div>
                                        <div class="fw-semibold fs-6 text-gray-400">Total Lamaran</div>
                                    </div>
                                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="fs-2 fw-bold">{{ $applicant->pendidikans->count() }}</div>
                                        </div>
                                        <div class="fw-semibold fs-6 text-gray-400">Riwayat Pendidikan</div>
                                    </div>
                                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="fs-2 fw-bold">{{ $applicant->dokumens->count() }}</div>
                                        </div>
                                        <div class="fw-semibold fs-6 text-gray-400">Dokumen Pendukung</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5 active" data-bs-toggle="tab" href="#kt_tab_profile">Profil & Bio</a>
                    </li>
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#kt_tab_education">Pendidikan</a>
                    </li>
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#kt_tab_experience">Pengalaman</a>
                    </li>
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#kt_tab_history">Riwayat Lamaran</a>
                    </li>
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#kt_tab_skills">Keahlian</a>
                    </li>
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#kt_tab_docs">Dokumen</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="col-12 mt-4">
        <div class="tab-content" id="myTabContent">
            <!-- Profile Info -->
            <div class="tab-pane fade show active" id="kt_tab_profile" role="tabpanel">
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title fw-bold text-gray-800">Biografi & Karakteristik</h3>
                    </div>
                    <div class="card-body py-4">
                        <div class="row mb-5">
                            <label class="col-12 fw-semibold text-muted mb-2">Deskripsi Diri</label>
                            <div class="col-12">
                                <span class="text-gray-800 fs-7 italic">{{ $applicant->deskripsidiri ?? 'Belum ada deskripsi profil.' }}</span>
                            </div>
                        </div>

                        <div class="separator separator-dashed my-5"></div>

                        <div class="row g-5">
                            <!-- Kolom 1 -->
                            <div class="col-md-6">
                                <div class="row mb-5">
                                    <label class="col-4 fw-semibold text-muted">Data Identitas</label>
                                    <div class="col-8">
                                        <span class="fw-bold fs-6 text-gray-800">NIK: {{ $applicant->noktp ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <label class="col-4 fw-semibold text-muted">Gender</label>
                                    <div class="col-8">
                                        <span class="text-gray-800 fs-6">{{ $applicant->jeniskelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <label class="col-4 fw-semibold text-muted">TTL</label>
                                    <div class="col-8">
                                        <span class="text-gray-800 fs-6">{{ $applicant->tempatlahir ?? '-' }}, {{ \Carbon\Carbon::parse($applicant->tanggallahir)->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <label class="col-4 fw-semibold text-muted">Fisik</label>
                                    <div class="col-8">
                                        <span class="text-gray-800 fs-7">T: {{ $applicant->tinggibadan ?? '0' }}cm / B: {{ $applicant->beratbadan ?? '0' }}kg</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom 2 -->
                            <div class="col-md-6">
                                <div class="row mb-5">
                                    <label class="col-4 fw-semibold text-muted">Kontak</label>
                                    <div class="col-8">
                                        <span class="fw-bold fs-6 text-success">{{ $applicant->nohp ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <label class="col-4 fw-semibold text-muted">Email</label>
                                    <div class="col-8">
                                        <span class="text-gray-800 fs-6">{{ $applicant->user->email ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <label class="col-4 fw-semibold text-muted">Domisili</label>
                                    <div class="col-8">
                                        <span class="text-gray-800 fs-7 leading-tight">{{ $applicant->alamatlengkap ?? '-' }}</span>
                                        @if($applicant->kelurahan)
                                            <div class="text-muted fs-9 mt-1 italic">
                                                {{ $applicant->kelurahan->nama }}, {{ $applicant->kelurahan->kecamatan->nama }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Education -->
            <div class="tab-pane fade" id="kt_tab_education" role="tabpanel">
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>Institusi / Sekolah</th>
                                        <th>Jenjang / Jurusan</th>
                                        <th class="text-end">Tahun Lulus</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @forelse($applicant->pendidikans as $edu)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fw-bold fs-6">{{ $edu->namasekolah }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-primary fw-bold">{{ $edu->kategori ?? 'Pendidikan' }}</span> | {{ $edu->jurusan }}
                                            </td>
                                            <td>{{ $edu->tahunawal }} - {{ $edu->tahunselesai }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-10 opacity-50">Data pendidikan tidak ditemukan.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Experience -->
            <div class="tab-pane fade" id="kt_tab_experience" role="tabpanel">
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>Perusahaan / Organisasi</th>
                                        <th>Posisi / Jabatan</th>
                                        <th>Periode Kerjas</th>
                                        <th class="text-end pe-5">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @forelse($applicant->pengalamans as $exp)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fw-bold fs-6">{{ $exp->namaperusahaan }}</span>
                                                </div>
                                            </td>
                                            <td><span class="text-dark fw-bold">{{ $exp->posisi }}</span></td>
                                            <td>{{ $exp->tahunawal }} - {{ $exp->tahunselesai ?: 'Sekarang' }}</td>
                                            <td class="text-end pe-5">
                                                <span class="badge {{ $exp->aktif ? 'badge-light-success' : 'badge-light-dark' }}">
                                                    {{ $exp->aktif ? 'Aktif' : 'Non-Aktif' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-10 opacity-50">Data pengalaman kerja tidak ditemukan.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application History -->
            <div class="tab-pane fade" id="kt_tab_history" role="tabpanel">
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>Posisi Lowongan</th>
                                        <th>Nama Perusahaan</th>
                                        <th>Event / Placement</th>
                                        <th>Waktu Submit</th>
                                        <th class="text-end">Status Audit</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @forelse($applicant->lamarans as $lamaran)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fw-bold fs-6">{{ $lamaran->lowongan->namalowongan ?? 'Data Hilang' }}</span>
                                                    <span class="text-muted fs-8">Gaji: Rp {{ number_format($lamaran->lowongan->gaji_min ?? 0, 0, ',', '.') }}</span>
                                                </div>
                                            </td>
                                            <td><span class="badge badge-light-primary fw-bold">{{ $lamaran->lowongan->register->perusahaan->namaperusahaan ?? '-' }}</span></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fs-7 fw-bold">{{ $lamaran->lowongan->register->even->namaperiode ?? 'External' }}</span>
                                                </div>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($lamaran->created_at)->format('d/m/Y H:i') }}</td>
                                            <td class="text-end">
                                                <span class="badge badge-light-info fw-bold">TERKIRIM (PENDING)</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-10 opacity-50">Belum ada riwayat lamaran yang dilakukan.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="tab-pane fade" id="kt_tab_docs" role="tabpanel">
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-body py-4">
                        <div class="row g-5">
                            @forelse($applicant->dokumens as $doc)
                                <div class="col-md-4">
                                    <div class="p-6 border border-gray-300 border-dashed rounded d-flex align-items-center mb-0">
                                        <i class="material-icons fs-2tx text-primary me-4">description</i>
                                        <div class="d-flex flex-column flex-grow-1">
                                            <span class="text-gray-800 fs-6 fw-bold mb-1">{{ $doc->namadokumen }}</span>
                                            <a href="{{ asset('storage/'.$doc->file) }}" target="_blank" class="link-primary fs-8 fw-semibold">Lihat Berkas <i class="material-icons fs-9 ms-1">open_in_new</i></a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-10 opacity-50">Dokumen pendukung belum diunggah.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skills -->
            <div class="tab-pane fade" id="kt_tab_skills" role="tabpanel">
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-body py-4">
                        <div class="row g-5">
                            @forelse($applicant->skills as $skill)
                                <div class="col-md-4">
                                    <div class="p-5 border border-gray-300 border-dashed rounded d-flex align-items-center mb-0 bg-light-sm">
                                        <div class="symbol symbol-40px me-4">
                                            <div class="symbol-label bg-light-info">
                                                <i class="material-icons text-info">star</i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1">
                                            <span class="text-gray-800 fs-6 fw-bold mb-1">{{ $skill->namaskill }}</span>
                                            <span class="badge badge-light-info fw-bold fs-9 w-fit text-uppercase">{{ $skill->level }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-10 opacity-50">Data keahlian belum ditambahkan.</div>
                            @endforelse
                        </div>
                    </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<div class="modal fade" id="modalChat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bolder fs-3">Kirim Pesan ke {{ $applicant->namalengkap }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formChat" action="{{ $mailAction }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="mb-5">
                        <label class="form-label fw-bold">Subjek Pesan</label>
                        <input type="text" name="subject" class="form-control" placeholder="Contoh: Undangan Wawancara - FindTalen" required>
                    </div>
                    <div>
                        <label class="form-label fw-bold">Isi Pesan</label>
                        <textarea name="message" class="form-control" rows="6" placeholder="Tuliskan pesan Anda di sini..." required></textarea>
                        <div class="text-muted fs-9 mt-2 italic">*Pesan ini akan dikirimkan langsung ke alamat email pelamar.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-8">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnKirimChat">
                        <span class="indicator-label">Kirim Sekarang</span>
                        <span class="indicator-progress d-none">
                            Mohon tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#formChat').on('submit', function(e) {
        e.preventDefault();
        let btn = $('#btnKirimChat');
        btn.find('.indicator-label').addClass('d-none');
        btn.find('.indicator-progress').removeClass('d-none');
        btn.prop('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Terkirim!',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    $('#modalChat').modal('hide');
                    $('#formChat')[0].reset();
                });
            },
            error: function(err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem.'
                });
            },
            complete: function() {
                btn.find('.indicator-label').removeClass('d-none');
                btn.find('.indicator-progress').addClass('d-none');
                btn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush

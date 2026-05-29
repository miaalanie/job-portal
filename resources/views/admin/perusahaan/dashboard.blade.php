@extends('layouts.admin')

@section('title', 'Dashboard Perusahaan')

@section('content')
<div class="row g-5 g-xl-10">
    <!-- Welcome Header: Modern Hero Style -->
    <div class="col-xxl-12 mb-5">
        <div class="card card-flush h-md-100 border-0 shadow-sm" style="background: linear-gradient(112.14deg, {{ $company->primary_color ?? '#7f1d1d' }} 0%, {{ $company->secondary_color ?? '#450a0a' }} 100%);">
            <div class="card-body d-flex flex-column justify-content-center p-10 position-relative">
                <div class="position-absolute top-0 end-0 opacity-10 p-10">
                    <i class="material-icons fs-5tx text-white">business_center</i>
                </div>
                <!-- Branding & Progress info -->
                <div class="d-flex align-items-center mb-5">
                    <div class="symbol symbol-70px me-5">
                        <div class="symbol-label fs-2 fw-bold bg-white bg-opacity-10 text-white">
                            {{ substr($dashboardData['companyName'], 0, 1) }}
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <h1 class="fw-bold text-white fs-2qx mb-1">Selamat Datang, {{ $dashboardData['companyName'] }}!</h1>
                        <span class="text-white opacity-75 fw-semibold fs-4">Halo {{ Auth::user()->name }}, senang melihat Anda kembali di Portal Mitra {{ $company->nama_perusahaan ?? 'Platform' }}.</span>
                    </div>
                </div>

                @if(!$dashboardData['isValidated'])
                    <div class="alert bg-white bg-opacity-20 d-flex flex-column flex-sm-row p-7 rounded-3 border-0 mt-5">
                        <i class="material-icons fs-2tx text-warning me-4 mb-5 mb-sm-0">warning</i>
                        <div class="d-flex flex-column pe-0 pe-sm-10">
                            <h4 class="fw-bold text-white fs-4 mb-2">Profil Perusahaan Belum Terverifikasi</h4>
                            <span class="text-white opacity-90 fs-6">Silakan lengkapi biodata dan dokumen legalitas agar Anda dapat mulai memposting lowongan kerja di platform kami.</span>
                        </div>
                        <div class="ms-sm-auto mt-5 mt-sm-0 d-flex align-items-center">
                            <a href="{{ route('admin.perusahaan.profile') }}" class="btn btn-warning fw-bold px-8 shadow-sm">Lengkapi Sekarang!</a>
                        </div>
                    </div>
                @else
                   <div class="d-flex gap-4 mt-5">
                       <a href="{{ route('admin.perusahaan.profile') }}" class="btn btn-white bg-opacity-10 text-white border-0 fw-bold px-6">Edit Profil</a>
                       <button class="btn btn-white bg-opacity-10 text-white border-0 fw-bold px-6">Bantuan & FAQ</button>
                   </div>
                @endif
            </div>
        </div>
    </div>

    @if($dashboardData['isValidated'])
    <!-- Main Performance stats -->
    <div class="col-md-6 col-lg-4 col-xl-4 mb-5">
        <div class="card card-flush h-md-100 shadow-sm border-0 bg-white hover-up-light">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $dashboardData['totalLowongan'] }}</span>
                    <span class="text-gray-400 pt-1 fw-semibold fs-6">Lowongan Aktif</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0 mt-n10">
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                        <span class="fw-bold fs-6 text-gray-400">Total terpublikasi</span>
                        <span class="fw-bold fs-6">{{ $dashboardData['totalLowongan'] }}</span>
                    </div>
                    <div class="h-8px mx-3 w-100 bg-light-success rounded">
                        <div class="bg-success rounded h-8px" role="progressbar" style="width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-4 mb-5">
        <div class="card card-flush h-md-100 shadow-sm border-0 bg-white hover-up-light">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $dashboardData['totalPelamar'] }}</span>
                    <span class="text-gray-400 pt-1 fw-semibold fs-6">Kandidat Masuk</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0 mt-n10">
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="d-flex justify-content-between w-100 mt-auto mb-2 text-primary">
                        <span class="fw-bold fs-6">Tingkat Serapan</span>
                        <span class="fw-bold fs-6">Aktif</span>
                    </div>
                    <div class="h-8px mx-3 w-100 bg-light-primary rounded">
                        <div class="bg-primary rounded h-8px" role="progressbar" style="width: 75%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-xl-4 mb-5">
        <div class="card card-flush h-md-100 shadow-sm border-0" style="background-color: #f7f9fc;">
            <div class="card-body p-8">
                <h3 class="fw-bold text-gray-800 mb-5 d-flex align-items-center">
                    <i class="material-icons text-primary me-2">update</i> Notifikasi Cepat
                </h3>
                <div class="scroll-y mh-150px">
                    <div class="d-flex align-items-start mb-5">
                        <span class="bullet bullet-vertical bg-primary h-40px me-3 w-5px"></span>
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fw-bold fs-7">Verifikasi Dokumen NIB</span>
                            <span class="text-muted fs-8 fw-semibold">Dokument sedang dalam review Admin.</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-5">
                        <span class="bullet bullet-vertical bg-success h-40px me-3 w-5px"></span>
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fw-bold fs-7">Event Job Fair Sukabumi</span>
                            <span class="text-muted fs-8 fw-semibold">Booth Anda telah disetujui.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ongoing Registrations & Invoices Section -->
    <div class="col-xxl-12 mt-5">
        <div class="card card-flush h-md-100 border-0 shadow-sm mb-10">
            <div class="card-header pt-7 px-10">
                <h3 class="card-title fw-bold text-gray-800">
                    <i class="material-icons text-success me-2">receipt_long</i> Status Registrasi & Invoice
                </h3>
            </div>
            <div class="card-body px-10 pt-5 pb-5">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-4 min-w-200px rounded-start">Nama Event</th>
                                <th class="min-w-125px">Paket / Opsi</th>
                                <th class="min-w-125px">Biaya Investasi</th>
                                <th class="min-w-125px">Status Aktivasi</th>
                                <th class="min-w-150px text-end pe-4 rounded-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dashboardData['registeredEvents'] as $reg)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <span class="symbol-label bg-light-success text-success"><i class="material-icons">event_note</i></span>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $reg->even->namaperiode }}</a>
                                                <span class="text-muted fw-semibold text-muted d-block fs-7">{{ \Carbon\Carbon::parse($reg->tanggalregister)->format('d M Y') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-gray-800 fw-bold d-block fs-6">{{ $reg->namapaket }}</span>
                                    </td>
                                    <td>
                                        @if($reg->biaya > 0)
                                            <span class="text-danger fw-bold fs-6">Rp {{ number_format($reg->biaya, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-success fw-bold fs-6 small text-uppercase">Gratis</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($reg->aktivasi == 1)
                                            <span class="badge badge-light-success fw-bold px-4 py-2">Aktif / Terverifikasi</span>
                                        @else
                                            <span class="badge badge-light-warning fw-bold px-4 py-2">Menunggu Validasi</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            @if($reg->aktivasi == 1)
                                                <span class="badge badge-light-success fw-bold px-4 py-2"><i class="material-icons fs-7 text-success me-1">check_circle</i> Selesai</span>
                                            @elseif($reg->payment)
                                                @php 
                                                    $statusClass = 'badge-light-info';
                                                    if($reg->payment->status == 'Ditolak') $statusClass = 'badge-light-danger';
                                                    if($reg->payment->status == 'Terverifikasi') $statusClass = 'badge-light-success';
                                                @endphp
                                                <span class="badge {{ $statusClass }} fw-bold px-4 py-2" title="Cairan: {{ $reg->payment->catatan ?? '-' }}">
                                                    <i class="material-icons fs-7 me-1">hourglass_empty</i> Menunggu Persetujuan
                                                </span>
                                            @elseif($reg->biaya > 0)
                                                <button type="button" class="btn btn-sm btn-info fw-bold rounded-pill px-5" onclick="showPaymentModal({{ $reg->id }}, '{{ $reg->even->namaperiode }}', {{ $reg->biaya }})">
                                                    Konfirmasi Bayar
                                                </button>
                                            @else
                                                <span class="badge badge-light-primary fw-bold px-4 py-2"><i class="material-icons fs-7 text-primary me-1">verified</i> Free Access</span>
                                            @endif
                                            
                                            <a href="{{ route('admin.perusahaan.invoice.download', encrypt($reg->id)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm rounded-circle" title="Download Invoice">
                                                <i class="material-icons fs-5">download</i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-10 text-muted italic">Belum ada pendaftaran event yang sedang berjalan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Job Fair Events: Interactive Section -->
    <div class="col-xxl-12 mt-5">
        <div class="card card-flush h-md-100 border-0 shadow-sm mb-10 overflow-hidden">
             <div class="card-header pt-7 px-10">
                <h3 class="card-title fw-bold text-gray-800">
                    <i class="material-icons text-primary me-2">local_activity</i> Event & Bursa Kerja Tersedia
                </h3>
            </div>
            <div class="card-body px-10 pt-5 pb-5">
                <div class="row g-7">
                    @forelse($dashboardData['activeEvents'] as $even)
                        <div class="col-lg-6 col-xxl-4">
                            <div class="event-card rounded-4 border p-6 position-relative overflow-hidden">
                                <div class="event-glow"></div>
                                <div class="d-flex align-items-center mb-6">
                                    <div class="symbol symbol-60px me-5">
                                        @if($even->gambar)
                                            <img src="{{ asset('storage/' . $even->gambar) }}" alt="Event Logo" class="object-fit-cover w-60px h-60px rounded">
                                        @else
                                            <div class="symbol-label bg-light-danger text-danger">
                                                <i class="material-icons text-danger">event</i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column">
                                        <h4 class="fw-bold text-gray-800 mb-1">{{ $even->namaperiode }}</h4>
                                        @if(in_array($even->id, $dashboardData['registeredEventIds']))
                                            <div class="badge badge-light-primary fs-8 fw-bold px-3 py-1 align-self-start">Terdaftar</div>
                                        @else
                                            <div class="badge badge-light-success fs-8 fw-bold px-3 py-1 align-self-start">Pendaftaran Dibuka</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="vstack gap-3 mb-6">
                                    <div class="d-flex align-items-center text-gray-600 fs-7">
                                        <i class="material-icons fs-6 me-2 text-primary">place</i> {{ $even->lokasi }}
                                    </div>
                                    <div class="d-flex align-items-center text-gray-600 fs-7">
                                        <i class="material-icons fs-6 me-2 text-primary">calendar_month</i> 
                                        {{ \Carbon\Carbon::parse($even->tanggalawal)->format('d M') }} - {{ \Carbon\Carbon::parse($even->tanggalselesai)->format('d M Y') }}
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex -space-x-3 mb-0">
                                        <div class="avatar-stack"><img src="https://i.pravatar.cc/101" alt=""></div>
                                        <div class="avatar-stack"><img src="https://i.pravatar.cc/102" alt=""></div>
                                        <div class="avatar-plus">+100</div>
                                    </div>
                                    @if(in_array($even->id, $dashboardData['registeredEventIds']))
                                        <button class="btn btn-light-primary btn-sm fw-bold rounded-pill px-5 shadow-sm" disabled>Sudah Terdaftar</button>
                                    @else
                                        <a href="{{ route('admin.perusahaan.event.detail', encrypt($even->id)) }}" class="btn btn-primary btn-sm fw-bold rounded-pill px-5 shadow-sm">Daftar Event</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 py-10 text-center bg-light rounded-4 border border-dashed border-gray-400">
                           <i class="material-icons fs-3tx text-gray-300 mb-3">event_busy</i>
                           <h4 class="text-gray-600 fw-bold">Belum Ada Event Aktif Hari Ini</h4>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Payment Confirmation Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0 justify-content-between px-10 pt-10">
                <h3 class="fw-bold text-gray-800 m-0">Konfirmasi Pembayaran</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    <i class="material-icons fs-4">close</i>
                </div>
            </div>
            <div class="modal-body px-10 py-10">
                <p class="text-muted fw-semibold mb-8">Anda akan mengirimkan konfirmasi pendaftaran untuk event <strong id="modal-event-name" class="text-dark"></strong>.</p>
                
                <form action="{{ route('admin.perusahaan.payment.confirm') }}" method="POST" enctype="multipart/form-data" id="payment-form">
                    @csrf
                    <input type="hidden" name="idregister" id="modal-idregister">
                    
                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-bold mb-2">Bank Asal</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Contoh: BCA / Mandiri / BNI" name="bank_asal" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-bold mb-2">Nama Pengirim (Sesuai Rekening)</label>
                            <input type="text" class="form-control form-control-solid" name="nama_pengirim" required />
                        </div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-bold mb-2">Jumlah Bayar (IDR)</label>
                            <input type="number" class="form-control form-control-solid" name="jumlah_bayar" id="modal-amount" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-bold mb-2">Tanggal Bayar</label>
                            <input type="date" class="form-control form-control-solid" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required />
                        </div>
                    </div>

                    <div class="fv-row mb-8">
                        <label class="required fs-6 fw-bold mb-2">Bukti Bayar (JPG/PNG/PDF)</label>
                        <input type="file" class="form-control form-control-solid" name="bukti_bayar" accept="image/*,.pdf" required />
                        <div class="text-muted fs-8 mt-1">Maksimum ukuran file: 2MB</div>
                    </div>

                    <div class="fv-row mb-10">
                        <label class="fs-6 fw-bold mb-2">Catatan Tambahan (Opsional)</label>
                        <textarea class="form-control form-control-solid" name="catatan" rows="3" placeholder="Tambahkan informasi tambahan jika diperlukan"></textarea>
                    </div>

                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-10 fw-bold" id="pj-submit">
                            <span class="indicator-label">Kirim Konfirmasi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .event-card { transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1); background: #ffffff; cursor: pointer; border-color: #f1f1f1 !important; z-index: 1; position: relative; }
    .event-card:hover { transform: translateY(-5px); border-color: #00796B !important; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1); }
    .event-glow { position: absolute; width: 100px; height: 100px; background: #00796B; filter: blur(60px); opacity: 0; bottom: -50px; right: -50px; transition: opacity 0.3s; z-index: -1; }
    .event-card:hover .event-glow { opacity: 0.15; }
    .hover-up-light:hover { transform: translateY(-3px); transition: transform 0.2s; }
    .avatar-stack { width: 32px; height: 32px; border-radius: 50%; border: 2px solid #fff; overflow: hidden; margin-left: -12px; }
    .avatar-stack:first-child { margin-left: 0; }
    .avatar-stack img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-plus { width: 32px; height: 32px; border-radius: 50%; border: 2px solid #fff; background: #f1f1f1; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; margin-left: -12px; color: #666; }
    .-space-x-3 { display: flex; padding-left: 12px; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endsection

@push('scripts')
<script>
    function showPaymentModal(idregister, eventName, amount) {
        document.getElementById('modal-idregister').value = idregister;
        document.getElementById('modal-event-name').innerText = eventName;
        document.getElementById('modal-amount').value = amount;
        
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }

    $(document).ready(function() {
        $('#payment-form').on('submit', function() {
            NProgress.start();
            $('#pj-submit').attr('disabled', true);
            $('#pj-submit').html('<span class="spinner-border spinner-border-sm me-2"></span> Mengirim...');
        });
    });
</script>
@endpush

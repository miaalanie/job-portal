@extends('layouts.admin')

@section('title', 'Evaluasi Sistem Rekomendasi')
@section('page_title', 'Evaluasi Sistem Rekomendasi')

@section('content')
<div class="card shadow-sm mb-6">
    <div class="card-header">
        <h3 class="card-title">Evaluasi Offline Sistem Rekomendasi</h3>
        <div class="card-toolbar">
            <span class="badge badge-light-primary">{{ $sampleCount }} pelamar siap diuji</span>
        </div>
    </div>
    <div class="card-body">
        <p class="text-gray-700 mb-4">
            Evaluasi ini menguji ulang rekomendasi menggunakan histori aplikasi. Lowongan yang diterima dipakai sebagai <strong>relevan</strong>, sedangkan lamaran dan wishlist lain dipakai sebagai histori pelatihan. Nilai dihitung pada top-K rekomendasi.
        </p>
        <div class="alert alert-light-warning mb-0">
            <strong>Catatan interpretasi:</strong> ini adalah evaluasi ranking, bukan klasifikasi. Karena itu metrik utamanya Precision@K, Recall@K, F1@K, Hit Rate@K, dan Coverage. CBF menggunakan kecocokan profil; CF menggunakan pola lowongan yang dipilih pelamar lain.
        </div>
        <div class="d-flex align-items-center gap-4 mt-6">
            <form method="POST" action="{{ route('admin.recommendation.evaluation.run') }}" onsubmit="this.querySelector('button').disabled = true; this.querySelector('.run-label').classList.add('d-none'); this.querySelector('.run-loading').classList.remove('d-none');">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="material-icons fs-5 me-1">play_arrow</i>
                    <span class="run-label">Jalankan Evaluasi</span>
                    <span class="run-loading d-none">Sedang menghitung...</span>
                </button>
            </form>
            <span class="text-muted fs-7">{{ $activeVacancyCount }} lowongan aktif tersedia sebagai kandidat.</span>
        </div>
        @if($sampleCount === 0 || $activeVacancyCount === 0)
            <div class="alert alert-light-danger mt-6 mb-0">
                <strong>Data belum cukup:</strong> evaluasi membutuhkan minimal satu pelamar dengan lamaran berstatus diterima dan satu lowongan aktif.
            </div>
        @endif
    </div>
</div>

@if($hasRun && $metrics)
@foreach([5, 10] as $k)
    <div class="card shadow-sm mb-6">
        <div class="card-header">
            <h3 class="card-title">Hasil Top-{{ $k }}</h3>
            <div class="card-toolbar text-muted fs-7">Semua nilai dalam persen</div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle mb-0">
                    <thead>
                        <tr class="text-muted fw-bold fs-7 text-uppercase">
                            <th class="ps-6">Algoritma</th>
                            <th>Pelamar diuji</th>
                            <th>Precision&#64;{{ $k }}</th>
                            <th>Recall&#64;{{ $k }}</th>
                            <th>F1&#64;{{ $k }}</th>
                            <th>Hit Rate&#64;{{ $k }}</th>
                            <th class="pe-6">Coverage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['cbf' => 'Content-Based Filtering (CBF)', 'cf' => 'Collaborative Filtering (CF)'] as $key => $label)
                            @php($metric = $metrics[$k][$key])
                            <tr>
                                <td class="ps-6 fw-bold text-gray-800">{{ $label }}</td>
                                <td>{{ $metric['evaluated_users'] }}</td>
                                <td><span class="badge badge-light-primary">{{ number_format($metric['precision'], 2) }}%</span></td>
                                <td><span class="badge badge-light-info">{{ number_format($metric['recall'], 2) }}%</span></td>
                                <td><span class="badge badge-light-success">{{ number_format($metric['f1'], 2) }}%</span></td>
                                <td>{{ number_format($metric['hit_rate'], 2) }}%</td>
                                <td class="pe-6">{{ $metric['coverage'] === null ? '-' : number_format($metric['coverage'], 2) . '%' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endforeach
@else
<div class="card shadow-sm mb-6">
    <div class="card-body text-center py-12">
        <i class="material-icons fs-3x text-muted mb-4">query_stats</i>
        <h3 class="text-gray-800">Evaluasi belum dijalankan</h3>
        <p class="text-muted mb-0">Klik tombol <strong>Jalankan Evaluasi</strong> untuk menghitung performa CBF dan CF dari data historis.</p>
    </div>
</div>
@endif

<div class="card shadow-sm">
    <div class="card-header"><h3 class="card-title">Arti Metrik</h3></div>
    <div class="card-body">
        <div class="row g-5 text-gray-700">
            <div class="col-md-6 col-xl-4"><strong>Precision@K</strong><p class="mb-0">Dari K rekomendasi, berapa yang relevan. Tinggi berarti rekomendasi lebih tepat sasaran.</p></div>
            <div class="col-md-6 col-xl-4"><strong>Recall@K</strong><p class="mb-0">Dari seluruh lowongan relevan, berapa yang berhasil ditemukan sistem.</p></div>
            <div class="col-md-6 col-xl-4"><strong>F1@K</strong><p class="mb-0">Keseimbangan antara Precision dan Recall. Berguna saat keduanya sama-sama penting.</p></div>
            <div class="col-md-6 col-xl-4"><strong>Hit Rate@K</strong><p class="mb-0">Persentase pelamar yang mendapat minimal satu rekomendasi relevan.</p></div>
            <div class="col-md-6 col-xl-4"><strong>Coverage</strong><p class="mb-0">Seberapa luas katalog lowongan yang pernah muncul sebagai rekomendasi.</p></div>
            <div class="col-md-6 col-xl-4"><strong>Catatan data</strong><p class="mb-0">Hasil dipengaruhi jumlah data diterima, histori pengguna, dan sampel maksimal 20 pelamar.</p></div>
        </div>
    </div>
</div>
@endsection

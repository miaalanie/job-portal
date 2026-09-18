@extends('layouts.admin')

@section('title', 'Pengaturan Rekomendasi')
@section('page_title', 'Pengaturan Sistem Rekomendasi')

@section('content')
<div class="row g-6">
    <div class="col-12">

        {{-- ===== INTRO ===== --}}
        <div class="card shadow-sm mb-6">
            <div class="card-body">
                <h4 class="mb-2">Tentang Content-Based Filtering (CBF)</h4>
                <p class="text-gray-700 mb-0">
                    CBF menilai kecocokan antara pelamar dan lowongan berdasarkan empat faktor: kesesuaian profil, skill, pendidikan, dan pengalaman. Setiap faktor memiliki <strong>bobot</strong> yang menentukan seberapa besar pengaruhnya terhadap skor akhir. Total keempat bobot harus 100%.
                </p>
            </div>
        </div>

        {{-- ===== FORM BOBOT ===== --}}
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title">Bobot Penilaian</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('admin.recommendation.settings.update') }}" id="weightForm">
                    @csrf
                    @method('PUT')

                    {{-- Total indicator + stacked bar --}}
                    <div id="totalBadge" class="alert d-flex justify-content-between align-items-center mb-3">
                        <span>Total bobot saat ini</span>
                        <span id="totalValue" class="fs-4 fw-bold">0%</span>
                    </div>
                    <div class="progress mb-6" style="height: 10px;">
                        <div class="progress-bar bg-primary" id="bar_weight_semantic" role="progressbar" style="width: 0%"></div>
                        <div class="progress-bar bg-info" id="bar_weight_skill" role="progressbar" style="width: 0%"></div>
                        <div class="progress-bar bg-success" id="bar_weight_education" role="progressbar" style="width: 0%"></div>
                        <div class="progress-bar bg-warning" id="bar_weight_experience" role="progressbar" style="width: 0%"></div>
                    </div>

                    <div class="row g-5">
                        @foreach([
                            'weight_semantic' => [
                                'icon' => 'account_circle',
                                'color' => 'primary',
                                'label' => 'Kesesuaian profil',
                                'help' => 'Mengukur kemiripan gambaran umum pelamar (CV dan minat) dengan deskripsi lowongan.',
                            ],
                            'weight_skill' => [
                                'icon' => 'build',
                                'color' => 'info',
                                'label' => 'Kecocokan skill',
                                'help' => 'Mengukur berapa banyak skill yang diminta lowongan dimiliki pelamar.',
                            ],
                            'weight_education' => [
                                'icon' => 'school',
                                'color' => 'success',
                                'label' => 'Pendidikan',
                                'help' => 'Mengukur kesesuaian jenjang dan jurusan pendidikan dengan syarat lowongan.',
                            ],
                            'weight_experience' => [
                                'icon' => 'work',
                                'color' => 'warning',
                                'label' => 'Pengalaman',
                                'help' => 'Mengukur kesesuaian posisi dan lama pengalaman kerja dengan kebutuhan lowongan.',
                            ],
                        ] as $field => $info)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="material-icons text-{{ $info['color'] }}">{{ $info['icon'] }}</i>
                                    <label class="form-label fw-bold mb-0">{{ $info['label'] }}</label>
                                </div>
                                <div class="input-group mb-2">
                                    <input
                                        type="number"
                                        class="form-control weight-input"
                                        name="{{ $field }}"
                                        min="0" max="1" step="0.01"
                                        value="{{ old($field, $settings->{$field}) }}"
                                        data-target="{{ $field }}"
                                        required
                                    >
                                    <span class="input-group-text weight-percent">
                                        {{ number_format(old($field, $settings->{$field}) * 100, 0) }}%
                                    </span>
                                </div>
                                <div class="form-text">{{ $info['help'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    <button class="btn btn-primary mt-6" type="submit" id="saveBtn">
                        <i class="material-icons fs-5 me-1">save</i>Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.weight-input');
    const totalBadge = document.getElementById('totalBadge');
    const totalValue = document.getElementById('totalValue');
    const saveBtn = document.getElementById('saveBtn');

    function updateAll() {
        let total = 0;

        inputs.forEach(function (input) {
            const val = parseFloat(input.value) || 0;
            total += val;

            const percentValue = Math.round(val * 100);
            const percentEl = input.closest('.input-group').querySelector('.weight-percent');
            percentEl.textContent = percentValue + '%';

            const bar = document.getElementById('bar_' + input.dataset.target);
            if (bar) {
                bar.style.width = percentValue + '%';
            }
        });

        const totalPercent = Math.round(total * 100);
        const isValid = totalPercent === 100;

        totalValue.textContent = totalPercent + '%' + (isValid ? '' : ' (harus 100%)');

        totalBadge.classList.remove('alert-success', 'alert-danger');
        totalBadge.classList.add(isValid ? 'alert-success' : 'alert-danger');

        saveBtn.disabled = !isValid;
    }

    inputs.forEach(function (input) {
        input.addEventListener('input', updateAll);
    });

    updateAll();
});
</script>
@endsection
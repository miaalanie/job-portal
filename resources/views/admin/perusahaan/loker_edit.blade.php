@extends('layouts.admin')

@section('title', 'Edit Lowongan - ' . $loker->namalowongan)

@section('content')
<div class="row g-7 justify-content-center">
    <!-- Left Column: Manual Form -->
    <div class="col-lg-9">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800 fs-3">Perbarui Lowongan: {{ $loker->namalowongan }}</span>
                    <span class="text-muted fw-semibold fs-7 px-1">Modifikasi detail posisi pekerjaan Anda</span>
                </h3>
            </div>
            <form id="loker-form" action="{{ route('admin.perusahaan.loker.update', encrypt($loker->id)) }}" method="POST">
                @csrf
                <div class="card-body">

                    {{-- ===================== --}}
                    {{-- SECTION: INFORMASI DASAR --}}
                    {{-- ===================== --}}
                    <h4 class="fw-bold text-gray-800 mb-6">Informasi Dasar</h4>

                    <div class="mb-10">
                        <label class="required form-label">Nama Posisi / Lowongan</label>
                        <input type="text" name="namalowongan" class="form-control @error('namalowongan') is-invalid @enderror" placeholder="Contoh: Senior Backend Engineer" value="{{ old('namalowongan', $loker->namalowongan) }}" required>
                        @error('namalowongan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-10">
                        <div class="col-md-6">
                            <label class="required form-label">Kategori Pekerjaan</label>
                            <select name="idkategorilowongan" class="form-select @error('idkategorilowongan') is-invalid @enderror" data-control="select2" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('idkategorilowongan', $loker->idkategorilowongan) == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="required form-label">Penempatan Kerja</label>
                            <select name="kategorilokasi" class="form-select @error('kategorilokasi') is-invalid @enderror" required>
                                <option value="">Pilih Penempatan</option>
                                <option value="Dalam Negeri" {{ old('kategorilokasi', $loker->kategorilokasi) == 'Dalam Negeri' ? 'selected' : '' }}>Dalam Negeri (Domestic)</option>
                                <option value="Luar Negeri" {{ old('kategorilokasi', $loker->kategorilokasi) == 'Luar Negeri' ? 'selected' : '' }}>Luar Negeri (Overseas)</option>
                            </select>
                            @error('kategorilokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="separator separator-dashed my-10"></div>

                    {{-- ===================== --}}
                    {{-- SECTION: KOMPENSASI & KUOTA --}}
                    {{-- ===================== --}}
                    <h4 class="fw-bold text-gray-800 mb-6">Kompensasi & Kuota</h4>

                    <div class="row mb-10">
                        <div class="col-md-4">
                            <label class="form-label">Gaji Minimal (Rupiah)</label>
                            <input type="text" name="gaji_awal" class="form-control rupiah-mask" placeholder="Contoh: 5.000.000" value="{{ old('gaji_awal', $loker->gaji_awal ? number_format($loker->gaji_awal, 0, ',', '.') : '') }}">
                            <div class="fs-9 text-muted mt-1">Kosongkan jika dirahasiakan</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gaji Maksimal (Rupiah)</label>
                            <input type="text" name="gaji_akhir" class="form-control rupiah-mask" placeholder="Contoh: 15.000.000" value="{{ old('gaji_akhir', $loker->gaji_akhir ? number_format($loker->gaji_akhir, 0, ',', '.') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kuota (Jumlah Rekrutmen)</label>
                            <input type="number" name="kuota" class="form-control" placeholder="Kosongkan jika tak terbatas" value="{{ old('kuota', $loker->kuota) }}">
                        </div>
                    </div>

                    <div class="separator separator-dashed my-10"></div>

                    {{-- ===================== --}}
                    {{-- SECTION: PERSYARATAN PELAMAR --}}
                    {{-- ===================== --}}
                    <h4 class="fw-bold text-gray-800 mb-6">Persyaratan Pelamar</h4>

                    <div class="row mb-10">
                        <div class="col-md-6">
                            <label class="form-label">Preferensi Gender</label>
                            <select name="preferensi_gender" class="form-select">
                                <option value="Semua"
                                    {{ old('preferensi_gender', $loker->preferensi_gender ?? 'Semua') == 'Semua' ? 'selected' : '' }}>
                                    Semua
                                </option>
                                <option value="Laki-laki"
                                    {{ old('preferensi_gender', $loker->preferensi_gender ?? '') == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki
                                </option>
                                <option value="Perempuan"
                                    {{ old('preferensi_gender', $loker->preferensi_gender ?? '') == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pendidikan Minimal</label>
                            <select name="minimal_pendidikan" class="form-select">
                                <option value="">Tidak ada syarat</option>
                                @php
                                $pendidikan = [
                                1 => 'SD',
                                2 => 'SMP',
                                3 => 'SMA / SMK',
                                4 => 'D1',
                                5 => 'D2',
                                6 => 'D3',
                                7 => 'D4 / S1',
                                8 => 'S2',
                                9 => 'S3',
                                ];
                                @endphp

                                @foreach ($pendidikan as $key => $value)
                                <option value="{{ $key }}"
                                    {{ old('minimal_pendidikan', $loker->minimal_pendidikan ?? '') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-10">
                        <div class="col-md-6">
                            <label class="form-label">Usia Minimal</label>
                            <input
                                type="number"
                                name="usia_min"
                                class="form-control"
                                placeholder="Contoh: 20"
                                value="{{ old('usia_min', $loker->usia_min ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Usia Maksimal</label>
                            <input
                                type="number"
                                name="usia_max"
                                class="form-control"
                                placeholder="Contoh: 35"
                                value="{{ old('usia_max', $loker->usia_max ?? '') }}">
                        </div>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Jurusan yang Diterima</label>
                        <div class="fs-8 text-muted mb-2">Ketik nama jurusan, lalu tekan Enter jika belum ada di daftar — akan otomatis ditambahkan.</div>

                        @php
                        $selectedJurusans = old(
                        'jurusans',
                        isset($loker) ? $loker->jurusans->pluck('idjurusan')->toArray() : [],
                        );
                        @endphp

                        <select name="jurusans[]" id="select-jurusans" class="form-select js-select2-tags" data-placeholder="Pilih atau ketik jurusan baru" multiple>
                            @foreach ($masterJurusans as $jurusan)
                            <option value="{{ $jurusan->id }}"
                                {{ in_array($jurusan->id, $selectedJurusans) ? 'selected' : '' }}>
                                {{ $jurusan->namajurusan }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Skill yang Dibutuhkan</label>
                        <div class="fs-8 text-muted mb-2">Ketik nama skill, lalu tekan Enter jika belum ada di daftar — akan otomatis ditambahkan.</div>

                        @php
                        $selectedSkills = old(
                        'skills',
                        isset($loker) ? $loker->skills->pluck('idskill')->toArray() : [],
                        );
                        @endphp

                        <select name="skills[]" id="select-skills" class="form-select js-select2-tags" data-placeholder="Pilih atau ketik skill baru" multiple>
                            @foreach ($masterSkills as $skill)
                            <option value="{{ $skill->id }}"
                                {{ in_array($skill->id, $selectedSkills) ? 'selected' : '' }}>
                                {{ $skill->namaskill }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    @php
                    $exp = old(
                    'minimal_pengalaman_bulan',
                    isset($loker) ? $loker->minimal_pengalaman_bulan : 0,
                    );

                    $expYear = floor($exp / 12);
                    $expMonth = $exp % 12;
                    @endphp

                    <div class="mb-10">
                        <label class="form-label">Pengalaman Kerja</label>

                        <div class="border rounded p-5 bg-light-secondary bg-opacity-25">
                            <div class="form-check form-check-custom form-check-solid mb-4">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="freshGraduate"
                                    {{ $exp == 0 ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="freshGraduate">
                                    Fresh Graduate diterima
                                </label>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fs-7 text-muted">Tahun</label>
                                    <input
                                        type="number"
                                        min="0"
                                        id="exp_year"
                                        class="form-control"
                                        value="{{ $expYear }}"
                                        {{ $exp == 0 ? 'disabled' : '' }}>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 text-muted">Bulan</label>
                                    <input
                                        type="number"
                                        min="0"
                                        max="11"
                                        id="exp_month"
                                        class="form-control"
                                        value="{{ $expMonth }}"
                                        {{ $exp == 0 ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>

                        <input
                            type="hidden"
                            id="minimal_pengalaman_bulan"
                            name="minimal_pengalaman_bulan"
                            value="{{ $exp }}">
                    </div>

                    <div class="separator separator-dashed my-10"></div>

                    {{-- ===================== --}}
                    {{-- SECTION: DESKRIPSI --}}
                    {{-- ===================== --}}
                    <h4 class="fw-bold text-gray-800 mb-6">Deskripsi Pekerjaan</h4>

                    <div class="mb-2">
                        <label class="required form-label">Deskripsi / Tugas & Tanggung Jawab</label>
                        <div id="editor-container">
                            <textarea name="deskripsi" id="deskripsi-editor" class="form-control d-none">{{ old('deskripsi', $loker->deskripsi) }}</textarea>
                        </div>
                        @error('deskripsi') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {

                            const fresh = document.getElementById('freshGraduate');
                            const year = document.getElementById('exp_year');
                            const month = document.getElementById('exp_month');
                            const hidden = document.getElementById('minimal_pengalaman_bulan');

                            function syncExperience() {

                                if (fresh.checked) {
                                    year.disabled = true;
                                    month.disabled = true;

                                    year.value = 0;
                                    month.value = 0;
                                    hidden.value = 0;
                                } else {
                                    year.disabled = false;
                                    month.disabled = false;

                                    const y = parseInt(year.value || 0);
                                    const m = parseInt(month.value || 0);

                                    hidden.value = (y * 12) + m;
                                }
                            }

                            fresh.addEventListener('change', syncExperience);
                            year.addEventListener('input', syncExperience);
                            month.addEventListener('input', syncExperience);

                            syncExperience();
                        });
                    </script>
                </div>
                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('admin.perusahaan.event.my-detail', encrypt($loker->idregister)) }}" class="btn btn-light me-3">Batal</a>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    $(document).ready(function() {

        // Debug
        const loker = @json($loker -> toArray());
        console.log(loker);
        let editor;

        ClassicEditor
            .create(document.querySelector('#deskripsi-editor'), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
                    'undo', 'redo'
                ],
                placeholder: 'Tuliskan deskripsi pekerjaan, tanggung jawab, dan persyaratan di sini...'
            })
            .then(newEditor => {
                editor = newEditor;
            })
            .catch(error => {
                console.error(error);
            });

        // Form Progress Feedback
        $('#loker-form').on('submit', function() {
            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).addClass('btn-progress');
            if (typeof NProgress !== "undefined") NProgress.start();
        });

        // Rupiah Masking
        $('.rupiah-mask').on('input', function() {
            let val = $(this).val().replace(/[^0-9]/g, '');
            if (val) {
                val = parseInt(val).toLocaleString('id-ID');
                $(this).val(val);
            } else {
                $(this).val('');
            }
        });

        //AJAX SKILL DAN JURUSAN
        function initTagSelect($el, createUrl, fieldName) {
            $el.select2({
                tags: true,
                width: '100%',
                placeholder: $el.data('placeholder'),
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;

                    // Cegah duplikat dengan opsi yang sudah ada (case-insensitive)
                    var exists = $el.find('option').filter(function() {
                        return $(this).text().trim().toLowerCase() === term.toLowerCase();
                    }).length;

                    if (exists) return null;

                    return {
                        id: 'new:' + term,
                        text: term,
                        newTag: true
                    };
                }
            });

            $el.on('select2:select', function(e) {
                var data = e.params.data;
                if (!data.newTag) return;

                var $select = $(this);
                var $option = $select.find('option[value="' + data.id + '"]');

                $.ajax({
                    url: createUrl,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        [fieldName]: data.text
                    },
                    success: function(res) {
                        // buang option sementara (masih id "new:...")
                        $option.remove();

                        // jaga-jaga kalau id asli udah ada (misal duplikat dari server)
                        var $existing = $select.find('option[value="' + res.id + '"]');
                        if ($existing.length) {
                            $existing.prop('selected', true);
                        } else {
                            var newOption = new Option(res.text, res.id, true, true);
                            $select.append(newOption);
                        }

                        $select.trigger('change');
                    },
                    error: function(xhr) {
                        $option.remove();
                        $select.trigger('change');
                        var msg = xhr.responseJSON?.message ?? 'Gagal menambahkan data baru.';
                        alert(msg);
                    }
                });
            });
        }

        initTagSelect(
            $('#select-jurusans'),
            "{{ route('admin.perusahaan.loker.master-jurusan.store') }}",
            'namajurusan'
        );

        initTagSelect(
            $('#select-skills'),
            "{{ route('admin.perusahaan.loker.master-skill.store') }}",
            'namaskill'
        );
    });
</script>
@endpush
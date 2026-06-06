{{-- resources/views/admin/perusahaan/partials/loker_applicants_table.blade.php --}}

{{-- ML Error banner --}}
@if($mlError)
<div class="alert alert-warning d-flex align-items-center mb-5" role="alert">
    <i class="material-icons me-2 fs-5">warning_amber</i>
    <div>
        Ranking tidak tersedia: <strong>{{ $mlError }}</strong>.
        Data pelamar tetap ditampilkan tanpa skor kecocokan.
    </div>
</div>
@endif

<div class="table-responsive">
    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
        <thead>
            <tr class="fw-bold text-muted bg-light">
                <th class="ps-4 min-w-250px">Nama & Info Pelamar</th>
                <th class="min-w-120px">Tanggal Melamar</th>
                <th class="min-w-100px">Kategori Lokasi</th>
                <th class="min-w-120px">Rencana Datang</th>
                <th class="min-w-100px">Status</th>
                @if($rankedApplicants->isNotEmpty())
                <th class="min-w-150px">Kecocokan</th>
                @endif
                <th class="text-end pe-4 min-w-100px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
            // kalau ada ranking, urutkan lamarans sesuai rank
            // kalau tidak ada, tampilkan urutan default
            $lamarans = $rankedApplicants->isNotEmpty()
            ? $loker->lamarans->sortBy(function ($lamaran) use ($rankedApplicants) {
            $rank = $rankedApplicants->get($lamaran->pelamar->id ?? 0);
            return $rank ? $rank['rank'] : 9999;
            })
            : $loker->lamarans;
            @endphp

            @forelse($lamarans as $lamaran)
            @php
            $pelamar = $lamaran->pelamar;
            $rankData = $rankedApplicants->get($pelamar->id ?? 0);
            @endphp
            <tr>
                {{-- Pelamar --}}
                <td class="ps-4">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-45px me-5">
                            @if($pelamar && $pelamar->foto)
                            <img src="{{ asset('storage/'.$pelamar->foto) }}" alt="Foto">
                            @else
                            <span class="symbol-label bg-light-danger text-danger fw-bold text-uppercase fs-5">
                                {{ substr($pelamar->namalengkap ?? '?', 0, 1) }}
                            </span>
                            @endif
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fw-bold fs-6">
                                {{ $pelamar->namalengkap ?? 'Tidak Ada Data' }}
                            </span>
                            <span class="text-muted fw-semibold fs-8">
                                {{ $pelamar->alamatlengkap ?? '-' }}
                            </span>
                        </div>
                    </div>
                </td>

                {{-- Tanggal Melamar --}}
                <td>
                    <div class="text-gray-600 fs-7 fw-bold">
                        {{ \Carbon\Carbon::parse($lamaran->tanggalmelamar)->format('d F Y') }}
                    </div>
                    <div class="text-muted fs-8">
                        {{ \Carbon\Carbon::parse($lamaran->tanggalmelamar)->diffForHumans() }}
                    </div>
                </td>

                {{-- Lokasi --}}
                <td>
                    <span class="text-gray-600 fs-7">{{ $loker->kategorilokasi }}</span>
                </td>

                {{-- Tanggal Hadir --}}
                <td>
                    @if($lamaran->tanggal_datang)
                    <span class="badge badge-light-primary fw-bold fs-8">
                        {{ \Carbon\Carbon::parse($lamaran->tanggal_datang)->format('d F Y') }}
                    </span>
                    @else
                    <span class="text-muted fs-9">-</span>
                    @endif
                </td>

                {{-- Status --}}
                <td>
                    @if($lamaran->statusditerima == 1)
                    <span class="badge badge-light-success fw-bold">Diterima</span>
                    @elseif($lamaran->statusditerima == 2)
                    <span class="badge badge-light-danger fw-bold">Ditolak</span>
                    @else
                    <span class="badge badge-light-warning fw-bold">Menunggu</span>
                    @endif
                </td>

                {{-- Kecocokan --}}
                @if($rankedApplicants->isNotEmpty())
                <td>
                    @if($rankData)
                    @php
                    $colorMap = ['green' => 'success', 'yellow' => 'warning', 'red' => 'danger'];
                    $badgeClass = $colorMap[$rankData['color']] ?? 'secondary';
                    @endphp
                    <div class="d-flex flex-column gap-1">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted fw-semibold fs-9">#{{ $rankData['rank'] }}</span>
                            <span class="badge badge-light-{{ $badgeClass }} fw-bold fs-8">
                                {{ $rankData['match_percentage'] }}% · {{ $rankData['label'] }}
                            </span>
                        </div>
                        {{-- score breakdown kecil --}}
                        <div class="text-muted fs-9">
                            Sem: {{ round($rankData['semantic_score'] * 100) }}% &nbsp;|&nbsp;
                            Skill: {{ round($rankData['skill_score'] * 100) }}% &nbsp;|&nbsp;
                            Edu: {{ round($rankData['education_score'] * 100) }}% &nbsp;|&nbsp;
                            Exp: {{ round($rankData['experience_score'] * 100) }}%
                        </div>
                        {{-- tags --}}
                        @if(!empty($rankData['tags']))
                        <div>
                            @foreach(array_slice($rankData['tags'], 0, 2) as $tag)
                            <span class="badge badge-light-{{ $tag['type'] }} fs-9 me-1">
                                {{ $tag['text'] }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @else
                    <span class="text-muted fs-9">—</span>
                    @endif
                </td>
                @endif

                {{-- Aksi --}}
                <td class="text-end pe-4">
                    @if($lamaran->pelamar)
                    <a href="{{ route('admin.perusahaan.pelamar.download-cv', encrypt($lamaran->pelamar->id)) }}"
                        class="btn btn-sm btn-light-primary fw-bold">
                        <i class="material-icons fs-5">description</i> CV
                    </a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $rankedApplicants->isNotEmpty() ? 7 : 6 }}"
                    class="text-center text-muted py-10">
                    Belum ada pelamar untuk lowongan ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
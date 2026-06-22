<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\Lowongan;
use App\Models\Kategorilowongan;
use App\Models\Kehadiran;
use App\Models\MasterSkill;
use App\Models\MasterJurusan;
use App\Services\MLMatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class PerusahaanLokerController extends Controller
{
    public function __construct(
        private MLMatchingService $mlService
    ) {}

    public function index(Request $request)
    {
        $idperusahaan = Auth::user()->idperusahaan;
        $q = $request->q;

        $lowongans = Lowongan::with(['register.even', 'kategori'])
            ->withCount('lamarans')
            ->whereHas('register', function ($query) use ($idperusahaan) {
                $query->where('idperusahaan', $idperusahaan);
            })
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sq) use ($q) {
                    $sq->where('namalowongan', 'LIKE', "%$q%")
                        ->orWhereHas('register.even', function ($eq) use ($q) {
                            $eq->where('namaperiode', 'LIKE', "%$q%");
                        });
                });
            })
            // Use join for sorting by event status
            ->join('registers', 'lowongans.idregister', '=', 'registers.id')
            ->join('evens', 'registers.idperiode', '=', 'evens.id')
            ->select('lowongans.*')
            ->orderBy('evens.statusaktif', 'desc')
            ->orderBy('lowongans.created_at', 'desc')
            ->paginate(10);

        return view('admin.perusahaan.loker_index', compact('lowongans', 'q'));
    }

    public function create($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $registration = Register::with('even')->findOrFail($decryptedId);

            // Security Check
            if ($registration->idperusahaan != Auth::user()->idperusahaan) {
                abort(403);
            }

            if ($registration->aktivasi == 0) {
                return redirect()->back()->with('error', 'Status pendaftaran event belum aktif. Anda baru dapat menambah lowongan setelah pendaftaran disetujui admin.');
            }

            $categories = Kategorilowongan::all();

            // --- TAMBAHAN BARU UNTUK FORM CREATE ---
            $masterSkills = \App\Models\MasterSkill::orderBy('namaskill')->get();
            $masterJurusans = \App\Models\MasterJurusan::orderBy('namajurusan')->get();

            // For Import: Get unique vacancies from other event registrations by this company
            $pastLowongans = Lowongan::whereHas('register', function ($q) {
                $q->where('idperusahaan', Auth::user()->idperusahaan);
            })
                ->where('idregister', '!=', $decryptedId)
                ->latest()
                ->get()
                ->unique('namalowongan');

            // Jangan lupa compact masterSkills dan masterJurusans ke view
            return view('admin.perusahaan.loker_create', compact('registration', 'categories', 'pastLowongans', 'masterSkills', 'masterJurusans'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat form tambah lowongan.');
        }
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'namalowongan' => 'required|max:255',
            'idkategorilowongan' => 'required',
            'kategorilokasi' => 'required',
            'deskripsi' => 'required',
            'kuota' => 'nullable|integer',
            'gaji_awal' => 'nullable',
            'gaji_akhir' => 'nullable',

            // --- VALIDASI TAMBAHAN BARU ---
            'minimal_pendidikan' => 'nullable|integer|between:1,9',
            'minimal_pengalaman_bulan' => 'required|integer|min:0',
            'preferensi_gender' => 'required|in:Semua,Laki-laki,Perempuan',
            'usia_min' => 'nullable|integer|min:0',
            'usia_max' => 'nullable|integer|min:0|gte:usia_min',
            'skills' => 'nullable|array',
            'jurusans' => 'nullable|array'
        ]);

        try {
            $decryptedId = Crypt::decrypt($id);
            $registration = Register::findOrFail($decryptedId);

            if ($registration->idperusahaan != Auth::user()->idperusahaan || $registration->aktivasi == 0) {
                abort(403);
            }

            // Sanitize Rupiah: Remove everything except numbers
            $gaji_awal = $request->gaji_awal ? preg_replace('/[^0-9]/', '', $request->gaji_awal) : null;
            $gaji_akhir = $request->gaji_akhir ? preg_replace('/[^0-9]/', '', $request->gaji_akhir) : null;

            // 1. Buat data Lowongan Utama
            $loker = Lowongan::create([
                'idregister' => $decryptedId,
                'namalowongan' => $request->namalowongan,
                'idkategorilowongan' => $request->idkategorilowongan,
                'kategorilokasi' => $request->kategorilokasi,
                'deskripsi' => $request->deskripsi,
                'kuota' => $request->kuota,
                'gaji_awal' => $gaji_awal,
                'gaji_akhir' => $gaji_akhir,
                'status' => 1,
                'useradd' => Auth::id(),

                // --- DATA KRITERIA BARU ---
                'minimal_pendidikan' => $request->minimal_pendidikan,
                'minimal_pengalaman_bulan' => $request->minimal_pengalaman_bulan,
                'preferensi_gender' => $request->preferensi_gender,
                'usia_min' => $request->usia_min ?? 0,
                'usia_max' => $request->usia_max ?? 0,
            ]);

            // 2. PROSES SAVE TABLE SKILLS (Mendukung Tag Baru)
            if ($request->has('skills')) {
                foreach ($request->skills as $skillInput) {
                    if (is_numeric($skillInput)) {
                        $idSkill = $skillInput;
                    } else {
                        // Jika user input teks baru, simpan ke MasterSkill dulu
                        $newSkill = \App\Models\MasterSkill::firstOrCreate(
                            ['namaskill' => trim($skillInput)]
                        );
                        $idSkill = $newSkill->id;
                    }

                    // Hubungkan ke Lowongan baru
                    $loker->skills()->create(['idskill' => $idSkill]);
                }
            }

            // 3. PROSES SAVE TABLE JURUSANS (Mendukung Tag Baru)
            if ($request->has('jurusans')) {
                foreach ($request->jurusans as $jurusanInput) {
                    if (is_numeric($jurusanInput)) {
                        $idJurusan = $jurusanInput;
                    } else {
                        // Jika user input teks baru, simpan ke MasterJurusan dulu
                        $newJurusan = \App\Models\MasterJurusan::firstOrCreate(
                            ['namajurusan' => trim($jurusanInput)]
                        );
                        $idJurusan = $newJurusan->id;
                    }

                    // Hubungkan ke Lowongan baru
                    $loker->jurusans()->create(['idjurusan' => $idJurusan]);
                }
            }

            return redirect()->route('admin.perusahaan.event.my-detail', $id)->with('success', 'Lowongan berhasil dipublikasikan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan lowongan: ' . $e->getMessage())->withInput();
        }
    }

    public function import(Request $request, $id)
    {
        $request->validate(['selected_lokers' => 'required|array']);

        try {
            $decryptedId = Crypt::decrypt($id);
            $registration = Register::findOrFail($decryptedId);

            if ($registration->idperusahaan != Auth::user()->idperusahaan || $registration->aktivasi == 0) {
                abort(403);
            }

            $count = 0;
            foreach ($request->selected_lokers as $lokerId) {
                $pastLoker = Lowongan::findOrFail($lokerId);

                // Double check ownership
                if ($pastLoker->register->idperusahaan != Auth::user()->idperusahaan) continue;

                Lowongan::create([
                    'idregister' => $decryptedId,
                    'namalowongan' => $pastLoker->namalowongan,
                    'idkategorilowongan' => $pastLoker->idkategorilowongan,
                    'kategorilokasi' => $pastLoker->kategorilokasi,
                    'deskripsi' => $pastLoker->deskripsi,
                    'kuota' => $pastLoker->kuota,
                    'gaji_awal' => $pastLoker->gaji_awal,
                    'gaji_akhir' => $pastLoker->gaji_akhir,
                    'status' => 1,
                    'useradd' => Auth::id(),
                ]);
                $count++;
            }

            return redirect()->route('admin.perusahaan.event.my-detail', $id)->with('success', "$count lowongan berhasil diimpor dari event sebelumnya.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor lowongan.');
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);

            $loker = Lowongan::with([
                'register',
                'skills.skill',
                'jurusans.jurusan',
            ])->findOrFail($decryptedId);

            if ($loker->register->idperusahaan != Auth::user()->idperusahaan) {
                abort(403);
            }

            $registration = $loker->register;
            $categories = Kategorilowongan::all();

            $masterSkills = MasterSkill::orderBy('namaskill')->get();
            $masterJurusans = MasterJurusan::orderBy('namajurusan')->get();

            return view(
                'admin.perusahaan.loker_edit',
                compact(
                    'loker',
                    'registration',
                    'categories',
                    'masterSkills',
                    'masterJurusans'
                )
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data lowongan.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'namalowongan' => 'required|max:255',
            'idkategorilowongan' => 'required',
            'kategorilokasi' => 'required',
            'deskripsi' => 'required',
            'kuota' => 'nullable|integer',
            'gaji_awal' => 'nullable',
            'gaji_akhir' => 'nullable',

            // --- VALIDASI TAMBAHAN BARU ---
            'minimal_pendidikan' => 'nullable|integer|between:1,9',
            'minimal_pengalaman_bulan' => 'required|integer|min:0',
            'preferensi_gender' => 'required|in:Semua,Laki-laki,Perempuan',
            'usia_min' => 'nullable|integer|min:0',
            'usia_max' => 'nullable|integer|min:0|gte:usia_min', // Usia max harus lebih besar/sama dengan usia min

            // --- VALIDASI UNTUK ARRAY SKILLS DAN JURUSANS ---
            'skills' => 'nullable|array',
            'jurusans' => 'nullable|array'
        ]);

        try {
            $decryptedId = Crypt::decrypt($id);
            $loker = Lowongan::with('register')->findOrFail($decryptedId);

            if ($loker->register->idperusahaan != Auth::user()->idperusahaan) {
                abort(403);
            }

            // Sanitize Rupiah
            $gaji_awal = $request->gaji_awal ? preg_replace('/[^0-9]/', '', $request->gaji_awal) : null;
            $gaji_akhir = $request->gaji_akhir ? preg_replace('/[^0-9]/', '', $request->gaji_akhir) : null;

            $loker->update([
                'namalowongan' => $request->namalowongan,
                'idkategorilowongan' => $request->idkategorilowongan,
                'kategorilokasi' => $request->kategorilokasi,
                'deskripsi' => $request->deskripsi,
                'kuota' => $request->kuota,
                'gaji_awal' => $gaji_awal,
                'gaji_akhir' => $gaji_akhir,
                'userupdate' => Auth::id(),

                // --- DATA TAMBAHAN BARU YANG DI-UPDATE ---
                'minimal_pendidikan' => $request->minimal_pendidikan,
                'minimal_pengalaman_bulan' => $request->minimal_pengalaman_bulan,
                'preferensi_gender' => $request->preferensi_gender,
                'usia_min' => $request->usia_min ?? 0,
                'usia_max' => $request->usia_max ?? 0,

            ]);

            // 2. PROSES UPDATE TABLE SKILLS (Mendukung Tag Baru via Relasi HasMany)
            $skillIds = [];
            if ($request->has('skills')) {
                foreach ($request->skills as $skillInput) {
                    if (is_numeric($skillInput)) {
                        $skillIds[] = $skillInput;
                    } else {
                        // Jika teks baru, simpan ke MasterSkill terlebih dahulu
                        $newSkill = \App\Models\MasterSkill::firstOrCreate(
                            ['namaskill' => trim($skillInput)]
                        );
                        $skillIds[] = $newSkill->id;
                    }
                }
            }

            // Hapus skill lama yang tidak dipilih lagi oleh user
            $loker->skills()->whereNotIn('idskill', $skillIds)->delete();

            // Tambahkan atau pastikan skill yang dipilih terdaftar di lowongan ini
            foreach ($skillIds as $idSkill) {
                $loker->skills()->updateOrCreate(
                    ['idskill' => $idSkill] // Jika kombinasi idlowongan & idskill sudah ada, biarkan. Jika belum, buat baru.
                );
            }


            // 3. PROSES UPDATE TABLE JURUSANS (Mendukung Tag Baru via Relasi HasMany)
            $jurusanIds = [];
            if ($request->has('jurusans')) {
                foreach ($request->jurusans as $jurusanInput) {
                    if (is_numeric($jurusanInput)) {
                        $jurusanIds[] = $jurusanInput;
                    } else {
                        // Jika teks baru, simpan ke MasterJurusan terlebih dahulu
                        $newJurusan = \App\Models\MasterJurusan::firstOrCreate(
                            ['namajurusan' => trim($jurusanInput)]
                        );
                        $jurusanIds[] = $newJurusan->id;
                    }
                }
            }

            // Hapus jurusan lama yang tidak dipilih lagi oleh user
            $loker->jurusans()->whereNotIn('idjurusan', $jurusanIds)->delete();

            // Tambahkan atau pastikan jurusan yang dipilih terdaftar di lowongan ini
            foreach ($jurusanIds as $idJurusan) {
                $loker->jurusans()->updateOrCreate(
                    ['idjurusan' => $idJurusan] // Jika kombinasi idlowongan & idjurusan sudah ada, biarkan. Jika belum, buat baru.
                );
            }

            return redirect()->route('admin.perusahaan.event.my-detail', encrypt($loker->idregister))->with('success', 'Lowongan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui lowongan: ' . $e->getMessage())->withInput();
        }
    }

    public function showApplicants($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);

            $loker = Lowongan::with([
                'register.even',
                'register.perusahaan',
                'kategori',
                'skills.skill',
                'jurusans.jurusan',
            ])
                ->withCount('lamarans')
                ->findOrFail($decryptedId);

            if ($loker->register->idperusahaan != Auth::user()->idperusahaan) {
                abort(403);
            }

            return view('admin.perusahaan.loker_applicants', compact('loker'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat detail pelamar: ' . $e->getMessage());
        }
    }
    
    public function loadApplicantsRanking($id): JsonResponse
    {
        try {
            $decryptedId = Crypt::decrypt($id);

            $loker = Lowongan::with([
                'register.perusahaan',
                'kategori',
                'lamarans.pelamar',
                'lamarans.pelamar.skills',
                'lamarans.pelamar.pendidikans',
                'lamarans.pelamar.pengalamans',
            ])
                ->withCount('lamarans')
                ->findOrFail($decryptedId);

            if ($loker->register->idperusahaan != Auth::user()->idperusahaan) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }

            $rankingResult   = $this->mlService->rankApplicants($loker);
            $rankedApplicants = $rankingResult['success']
                ? collect($rankingResult['ranked_applicants'])->keyBy('pelamar_id')
                : collect();

            $mlError = !$rankingResult['success']
                ? ($rankingResult['message'] ?? 'Gagal menghubungi ML service.')
                : null;

            // Merge data lamaran + ranking, format tanggal di sini
            $lamarans = $loker->lamarans
                ->map(function ($lamaran) use ($rankedApplicants) {
                    $pelamar  = $lamaran->pelamar;
                    $rankData = $rankedApplicants->get($pelamar?->id ?? 0);

                    return [
                        'namalengkap'         => $pelamar?->namalengkap ?? 'Tidak Ada Data',
                        'foto_url'            => $pelamar?->foto ? asset('storage/' . $pelamar->foto) : null,
                        'alamatlengkap'       => $pelamar?->alamatlengkap ?? '-',
                        'tanggalmelamar'      => \Carbon\Carbon::parse($lamaran->tanggalmelamar)->format('d F Y'),
                        'tanggalmelamar_diff' => \Carbon\Carbon::parse($lamaran->tanggalmelamar)->diffForHumans(),
                        'tanggal_datang'      => $lamaran->tanggal_datang
                            ? \Carbon\Carbon::parse($lamaran->tanggal_datang)->format('d F Y')
                            : null,
                        'statusditerima'      => $lamaran->statusditerima,
                        'cv_url'              => $pelamar
                            ? route('admin.perusahaan.pelamar.download-cv', encrypt($pelamar->id))
                            : null,
                        // Ranking — null kalau ML gagal
                        'rank'             => $rankData['rank'] ?? null,
                        'match_percentage' => $rankData['match_percentage'] ?? null,
                        'label'            => $rankData['label'] ?? null,
                        'color'            => $rankData['color'] ?? null,
                        'semantic_score'   => $rankData['semantic_score'] ?? null,
                        'skill_score'      => $rankData['skill_score'] ?? null,
                        'education_score'  => $rankData['education_score'] ?? null,
                        'experience_score' => $rankData['experience_score'] ?? null,
                        'tags'             => $rankData['tags'] ?? [],
                    ];
                })
                ->sortBy(fn($l) => $l['rank'] ?? 9999)
                ->values()
                ->toArray();

            return response()->json([
                'success'        => true,
                'total'          => $loker->lamarans_count,
                'has_ranking'    => $rankedApplicants->isNotEmpty(),
                'ml_error'       => $mlError,
                'kategorilokasi' => $loker->kategorilokasi,
                'lamarans'       => $lamarans,
            ]);
        } catch (\Exception $e) {
            Log::error('loadApplicantsRanking error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function attendance($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            // Include applicants (lamarans) with their related pelamar profiles and kehadirans
            $loker = Lowongan::with(['register.even', 'kategori', 'lamarans.pelamar', 'lamarans.kehadirans'])
                ->findOrFail($decryptedId);

            if ($loker->register->idperusahaan != Auth::user()->idperusahaan) {
                abort(403);
            }

            return view('admin.perusahaan.loker_attendance', compact('loker'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat daftar hadir: ' . $e->getMessage());
        }
    }

    public function updateAttendance(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $loker = Lowongan::with('lamarans')->findOrFail($decryptedId);

            if ($loker->register->idperusahaan != Auth::user()->idperusahaan) {
                abort(403);
            }

            $presentIds = $request->presents ?? []; // ids of lamarans checked as present

            foreach ($loker->lamarans as $lamaran) {
                if (in_array($lamaran->id, $presentIds)) {
                    Kehadiran::updateOrCreate(
                        ['idlamaran' => $lamaran->id],
                        [
                            'statushadir' => 1,
                            'jam' => now()->format('H:i:s'),
                            'tanggal' => now()->format('Y-m-d'),
                            'useradd' => Auth::id()
                        ]
                    );
                } else {
                    Kehadiran::where('idlamaran', $lamaran->id)->delete();
                }
            }

            return redirect()->back()->with('success', 'Kehadiran berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui kehadiran.');
        }
    }

    public function toggleStatus($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $loker = Lowongan::with('register')->findOrFail($decryptedId);

            // Security check
            if ($loker->register->idperusahaan != Auth::user()->idperusahaan) {
                abort(403);
            }

            if ($loker->register->aktivasi == 0) {
                return redirect()->back()->with('error', 'Status pendaftaran event belum aktif. Anda tidak dapat mengaktifkan/menutup lowongan.');
            }

            $loker->status = ($loker->status == 1) ? 0 : 1;
            $loker->save();

            $statusText = $loker->status == 1 ? 'diterbitkan' : 'ditutup';
            return redirect()->back()->with('success', "Status lowongan berhasil diperbarui menjadi $statusText.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah status lowongan.');
        }
    }

    public function storeSkill(Request $request)
    {
        $request->validate([
            'namaskill' => 'required|string|max:255',
        ]);

        $nama = trim($request->namaskill);

        $skill = MasterSkill::whereRaw('LOWER(namaskill) = ?', [strtolower($nama)])->first();

        if (!$skill) {
            $skill = MasterSkill::create(['namaskill' => $nama]);
        }

        return response()->json([
            'id'   => $skill->id,
            'text' => $skill->namaskill,
        ]);
    }

    public function storeJurusan(Request $request)
    {
        $request->validate([
            'namajurusan' => 'required|string|max:255',
        ]);

        $nama = trim($request->namajurusan);

        $jurusan = MasterJurusan::whereRaw('LOWER(namajurusan) = ?', [strtolower($nama)])->first();

        if (!$jurusan) {
            $jurusan = MasterJurusan::create(['namajurusan' => $nama]);
        }

        return response()->json([
            'id'   => $jurusan->id,
            'text' => $jurusan->namajurusan,
        ]);
    }
}

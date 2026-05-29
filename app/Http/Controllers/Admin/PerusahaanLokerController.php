<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\Lowongan;
use App\Models\Kategorilowongan;
use App\Models\Kehadiran;
use App\Services\MLMatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

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

            // For Import: Get unique vacancies from other event registrations by this company
            $pastLowongans = Lowongan::whereHas('register', function ($q) {
                $q->where('idperusahaan', Auth::user()->idperusahaan);
            })
                ->where('idregister', '!=', $decryptedId)
                ->latest()
                ->get()
                ->unique('namalowongan');

            return view('admin.perusahaan.loker_create', compact('registration', 'categories', 'pastLowongans'));
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

            Lowongan::create([
                'idregister' => $decryptedId,
                'namalowongan' => $request->namalowongan,
                'idkategorilowongan' => $request->idkategorilowongan,
                'kategorilokasi' => $request->kategorilokasi,
                'deskripsi' => $request->deskripsi,
                'kuota' => $request->kuota,
                'gaji_awal' => $gaji_awal,
                'gaji_akhir' => $gaji_akhir,
                'status' => 1, // Auto-active if registration is active
                'useradd' => Auth::id(),
            ]);

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
            $loker = Lowongan::with('register')->findOrFail($decryptedId);

            // Security check
            if ($loker->register->idperusahaan != Auth::user()->idperusahaan) {
                abort(403);
            }

            $registration = $loker->register;
            $categories = Kategorilowongan::all();

            return view('admin.perusahaan.loker_edit', compact('loker', 'registration', 'categories'));
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
            ]);

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
                'lamarans.pelamar',
                'lamarans.pelamar.skills',
                'lamarans.pelamar.pendidikans',
                'lamarans.pelamar.pengalamans',
            ])
                ->withCount('lamarans')
                ->findOrFail($decryptedId);

            if ($loker->register->idperusahaan != Auth::user()->idperusahaan) {
                abort(403);
            }

            // hit ML service untuk ranking
            $rankingResult = $this->mlService->rankApplicants($loker);

            // kalau ML service gagal, tetap tampilkan halaman
            // tapi $rankedApplicants kosong — blade handle state ini
            $rankedApplicants = $rankingResult['success']
                ? collect($rankingResult['ranked_applicants'])->keyBy('pelamar_id')
                : collect();

            $mlError = !$rankingResult['success']
                ? ($rankingResult['message'] ?? 'Gagal menghubungi ML service.')
                : null;

            return view('admin.perusahaan.loker_applicants', compact(
                'loker',
                'rankedApplicants',
                'mlError'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat detail pelamar: ' . $e->getMessage());
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
}

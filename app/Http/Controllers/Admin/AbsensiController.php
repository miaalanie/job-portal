<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Even;
use App\Models\Perusahaan;
use App\Models\Lowongan;
use App\Models\Lamaran;
use App\Models\Kehadiran;
use App\Models\Register;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdminEvent = $user->hasRole('Admin Event');

        if ($isAdminEvent) {
            $idperiode = $user->ideven;
            $events = Even::where('id', $idperiode)->get();
        } else {
            $idperiode = $request->idperiode;
            $events = Even::select('id', 'namaperiode')->get();
        }

        // Determine correct company context
        if ($user->hasRole('Admin Perusahaan')) {
            $idperusahaan = $user->idperusahaan;
        } else {
            $idperusahaan = $request->idperusahaan;
        }

        $query = Lowongan::with(['register.even', 'register.perusahaan', 'kategori'])
            ->withCount('lamarans');

        if ($idperiode) {
            $query->whereHas('register', function($q) use ($idperiode) {
                $q->where('idperiode', $idperiode);
            });
        }

        if ($idperusahaan) {
            $query->whereHas('register', function($q) use ($idperusahaan) {
                $q->where('idperusahaan', $idperusahaan);
            });
        }

        $vacancies = $query->latest()->get();
        
        // Only fetch list of companies if Superadmin/App Admin/Admin Event
        $companies = !$user->hasRole('Admin Perusahaan') ? Perusahaan::all() : collect();

        // Also restrict companies specifically for Admin Event if needed, but Perusahaan::all() is fine for dropdown
        if ($isAdminEvent) {
             $companies = Perusahaan::whereHas('registers', function($q) use ($idperiode) {
                 $q->where('idperiode', $idperiode);
             })->get();
        }

        return view('admin.absensi.index', compact('vacancies', 'events', 'companies', 'idperiode', 'idperusahaan', 'isAdminEvent'));
    }

    public function show($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $loker = Lowongan::with(['register.even', 'register.perusahaan', 'lamarans.pelamar', 'lamarans.kehadirans', 'lamarans.sesi'])
                ->findOrFail($decryptedId);

            // Security check for Company Admins
            $user = Auth::user();
            if ($user->hasRole('Admin Perusahaan') && $loker->register->idperusahaan != $user->idperusahaan) {
                abort(403, 'Anda tidak memiliki akses ke data lowongan ini.');
            }

            return view('admin.absensi.show', compact('loker'));
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) throw $e;
            return redirect()->back()->with('error', 'Detail lowongan tidak ditemukan atau ID tidak valid.');
        }
    }

    public function manualAbsen(Request $request, $idlowongan)
    {
        try {
            $decryptedId = Crypt::decrypt($idlowongan);
            $loker = Lowongan::with('register')->findOrFail($decryptedId);
            
            // Security check for Company Admins
            $user = Auth::user();
            if ($user->hasRole('Admin Perusahaan') && $loker->register->idperusahaan != $user->idperusahaan) {
                abort(403);
            }

            $presents = $request->presents ?? []; // Array of Lamaran IDs
            
            // Get all lamarans for this vacancy to handle "removal" of presence if unchecked
            $allLamarans = Lamaran::where('idlowongan', $decryptedId)->pluck('id')->toArray();

            foreach ($allLamarans as $lamaranId) {
                if (in_array($lamaranId, $presents)) {
                    Kehadiran::updateOrCreate(
                        ['idlamaran' => $lamaranId],
                        [
                            'statushadir' => 1,
                            'jam' => now()->format('H:i:s'),
                            'tanggal' => now()->format('Y-m-d'),
                            'useradd' => Auth::id()
                        ]
                    );
                } else {
                    Kehadiran::where('idlamaran', $lamaranId)->delete();
                }
            }

            return redirect()->back()->with('success', 'Daftar kehadiran berhasil diperbarui secara manual.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses absensi manual.');
        }
    }

    // This method will be triggered by Pelamar scanning QR
    public function scanAbsen($id_encrypted)
    {
        try {
            $idlowongan = Crypt::decrypt($id_encrypted);
            $user = Auth::user();

            if (!$user || !$user->hasRole('Pelamar')) {
                return redirect()->route('login')->with('info', 'Silakan login sebagai pelamar untuk melakukan absensi.');
            }

            $pelamar = $user->pelamar;
            if (!$pelamar) {
                return redirect()->route('pelamar.dashboard')->with('error', 'Profil pelamar tidak ditemukan.');
            }

            // Check if application exists
            $lamaran = Lamaran::with(['sesi', 'even'])->where('idpelamar', $pelamar->id)
                ->where('idlowongan', $idlowongan)
                ->first();

            if (!$lamaran) {
                return redirect()->route('pelamar.dashboard')->with('error', 'Anda belum melamar lowongan ini atau QR Code tidak terdaftar untuk posisi Anda.');
            }

            // 1. Date Verification (H-Day Check)
            $even = $lamaran->even;
            $today = now()->toDateString();
            $eventDateStart = \Carbon\Carbon::parse($even->tanggalawal)->toDateString();
            $eventDateEnd = \Carbon\Carbon::parse($even->tanggalselesai)->toDateString();

            if ($today < $eventDateStart) {
                return redirect()->route('pelamar.dashboard')->with('error', "Event pelaksanan '{$even->namaperiode}' belum dimulai (Mulai: {$eventDateStart}).");
            }

            if ($today > $eventDateEnd) {
                return redirect()->route('pelamar.dashboard')->with('error', "Event pelaksanan '{$even->namaperiode}' sudah berakhir pada {$eventDateEnd}.");
            }

            // 2. Session Verification if event has sessions active
            $sesi = $lamaran->sesi;
            if ($even->status_sesi == 1 && $sesi) {
                $now = now()->toTimeString();
                $start = $sesi->jam_mulai;
                $end = $sesi->jam_selesai;

                if ($now < $start || $now > $end) {
                    $namaSesi = $sesi->nama_sesi;
                    return redirect()->route('pelamar.dashboard')->with('error', "Maaf, waktu absensi sesi Anda ({$namaSesi}) adalah pukul {$start} s/d {$end}. Saat ini belum masuk jam sesi Anda atau sudah berakhir.");
                }
            }

            // Record attendance
            Kehadiran::updateOrCreate(
                ['idlamaran' => $lamaran->id],
                [
                    'statushadir' => 1,
                    'jam' => now()->format('H:i:s'),
                    'tanggal' => now()->format('Y-m-d'),
                    'useradd' => Auth::id()
                ]
            );

            return redirect()->route('pelamar.dashboard')->with('success', 'Kehadiran Anda berhasil tercatat. Selamat berjuang!');

        } catch (\Exception $e) {
            return redirect()->route('pelamar.dashboard')->with('error', 'QR Code tidak valid atau terjadi kesalahan.');
        }
    }
}

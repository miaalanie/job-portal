<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Even;
use Illuminate\Support\Facades\Auth;
use Exception;

class ApplyController extends Controller
{
    public function apply(Request $request)
    {
        $user = Auth::user();
        if (!$user->idpelamar) {
            return response()->json(['status' => 'error', 'message' => 'Lengkapi profil Anda sebelum melamar.'], 403);
        }

        $request->validate([
            'idlowongan' => 'required',
            'idsesi' => 'nullable',
            'tanggal_datang' => 'required|date'
        ]);

        try {
            $vacancy = Lowongan::with('register.even')->findOrFail($request->idlowongan);
            $event = $vacancy->register->even;

            // 1. Quota Check (Global Event)
            $totalEventApplicants = Lamaran::where('ideven', $event->id)->count();
            if ($event->kuota_maksimum > 0 && $totalEventApplicants >= $event->kuota_maksimum) {
                return response()->json(['status' => 'error', 'message' => 'Maaf, kuota pendaftaran untuk event ini sudah penuh.'], 422);
            }

            // 2. Maximum Apply Check (Per User per Event)
            $userApplyCount = Lamaran::where('idpelamar', $user->idpelamar)
                ->where('ideven', $event->id)
                ->count();
            
            if ($event->maksimum_apply > 0 && $userApplyCount >= $event->maksimum_apply) {
                return response()->json(['status' => 'error', 'message' => 'Anda telah mencapai batas maksimal lamaran untuk event ini (Maks: '.$event->maksimum_apply.').'], 422);
            }

            // 3. Duplicate Application Check
            $alreadyApplied = Lamaran::where('idpelamar', $user->idpelamar)
                ->where('idlowongan', $vacancy->id)
                ->exists();
            if ($alreadyApplied) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah melamar posisi ini.'], 422);
            }

            // 4. Create Application
            Lamaran::create([
                'idpelamar' => $user->idpelamar,
                'idlowongan' => $vacancy->id,
                'ideven' => $event->id,
                'idsesi' => ($event->status_sesi == 1) ? ($request->idsesi ?? 0) : 0,
                'tanggal_datang' => $request->tanggal_datang,
                'tanggalmelamar' => now()->toDateString(),
                'statusditerima' => '0',
                'useradd' => $user->id
            ]);

            // 5. Send Notification to Company Admin
            $companyUser = \App\Models\User::where('idperusahaan', $vacancy->register->idperusahaan)->first();
            if ($companyUser) {
                \Illuminate\Support\Facades\DB::table('system_notifications')->insert([
                    'user_id' => $companyUser->id,
                    'type' => 'lamaran_baru',
                    'title' => 'Lamaran Baru Masuk',
                    'message' => $user->pelamar->namalengkap . ' baru saja melamar untuk posisi ' . $vacancy->namalowongan,
                    'url' => route('admin.perusahaan.pelamar.index'),
                    'is_read' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Lamaran Anda berhasil dikirim ke ' . $vacancy->register->perusahaan->nama . '.'
            ]);

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}

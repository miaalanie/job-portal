<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Even;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasRole('Pelamar')) {
            return redirect()->route('home')->with('error', 'Akses ditolak.');
        }

        // Profile completion check
        if (!$user->idpelamar) {
            return redirect()->route('pelamar.complete-data')->with('warning', 'Silakan lengkapi profil Anda terlebih dahulu.');
        }

        $pelamar = $user->pelamar;
        $now = now();
        
        // 1. Precise Event Metrics Orchestration
        $allApplications = Lamaran::where('idpelamar', $user->idpelamar)
            ->with('even')
            ->get();
            
        $eventGroups = $allApplications->groupBy('ideven');
        
        $totalEventsFollowed = $eventGroups->filter(function($lams) use ($now) {
            $even = $lams->first()->even;
            return $even && \Carbon\Carbon::parse($even->tanggalselesai)->isPast();
        })->count();

        $upcomingGroups = $eventGroups->filter(function($lams) use ($now) {
            $even = $lams->first()->even;
            return $even && !\Carbon\Carbon::parse($even->tanggalselesai)->isPast();
        });
        
        $totalUpcomingEvents = $upcomingGroups->count();
        $totalUpcomingApplies = $upcomingGroups->sum->count();

        // 2. Nearest Event for Physical Presence Readiness
        $nextEvent = $upcomingGroups->sortBy(function($lams) {
            return $lams->first()->even->tanggalawal;
        })->first()?->first()?->even;

        // 3. Clinical Profile Completion Manifest
        $completion = 0;
        $coreFields = ['foto', 'noktp', 'namalengkap', 'nohp', 'tempatlahir', 'tanggallahir', 'jeniskelamin', 'idkelurahan', 'alamatlengkap'];
        foreach ($coreFields as $field) {
            if (!empty($pelamar->$field)) $completion += 10;
        }
        if ($pelamar->pendidikans()->exists()) $completion += 10;
        $profileCompletion = min(100, $completion);

        // Recent Applied Jobs
        $recentApplications = Lamaran::with(['lowongan.register.perusahaan', 'lowongan.kategori', 'even', 'sesi', 'kehadirans'])
            ->where('idpelamar', $user->idpelamar)
            ->latest()
            ->take(5)
            ->get();

        // Recommended Jobs
        // $recommendedJobs = Lowongan::with(['register.perusahaan', 'register.even', 'kategori'])
        //     ->whereHas('register', function($q) {
        //         $q->where('aktivasi', 1);
        //     })
        //     ->whereHas('register.even', function($q) {
        //         $q->where('statusaktif', 1);
        //     })
        //     ->latest()
        //     ->take(4)
        //     ->get();

        // Wishlisted Jobs
        $wishlistedJobs = \App\Models\Wishlist::with(['lowongan.register.perusahaan', 'lowongan.kategori', 'lowongan.register.even'])
            ->where('idpelamar', $user->idpelamar)
            ->latest()
            ->take(6)
            ->get();

        return view('pelamar.dashboard', compact(
            'user', 'pelamar', 'profileCompletion', 
            'totalEventsFollowed', 'totalUpcomingEvents', 'totalUpcomingApplies', 
            'nextEvent', 'recentApplications', 'wishlistedJobs', 'upcomingGroups'
        ));
    }

    public function printCard($ideven)
    {
        try {
            $decryptedId = \Illuminate\Support\Facades\Crypt::decrypt($ideven);
            $user = Auth::user();
            $pelamar = $user->pelamar;
            if (!$pelamar) abort(404);

            $event = Even::findOrFail($decryptedId);
            
            $lamarans = Lamaran::with(['lowongan.register.perusahaan', 'sesi'])
                ->where('idpelamar', $pelamar->id)
                ->where('ideven', $decryptedId)
                ->get();

            if ($lamarans->isEmpty()) {
                 return redirect()->back()->with('error', 'Anda belum melamar lowongan apa pun di event ini.');
            }

            return view('pelamar.card', compact('pelamar', 'event', 'lamarans'));
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function showApplicantStatus($encrypted_id, $ideven)
    {
        try {
            $idpelamar = \Illuminate\Support\Facades\Crypt::decrypt($encrypted_id);
            $decryptedEvenId = \Illuminate\Support\Facades\Crypt::decrypt($ideven);
            
            $pelamar = \App\Models\Pelamar::findOrFail($idpelamar);
            $event = Even::findOrFail($decryptedEvenId);
            
            $lamarans = Lamaran::with(['lowongan.register.perusahaan', 'sesi'])
                ->where('idpelamar', $idpelamar)
                ->where('ideven', $decryptedEvenId)
                ->get();

            return view('pelamar.public_status', compact('pelamar', 'event', 'lamarans'));
        } catch (\Exception $e) {
            abort(404);
        }
    }
}

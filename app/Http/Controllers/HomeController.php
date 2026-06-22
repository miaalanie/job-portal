<?php

namespace App\Http\Controllers;

use App\Models\Even;
use App\Models\Lowongan;
use App\Models\PengaturanPerusahaan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->hasRole('Pelamar') && !auth()->user()->idpelamar) {
            return redirect()->route('pelamar.complete-data')->with('warning', 'Silakan lengkapi profil Anda terlebih dahulu.');
        }

        // 1. Get Headline Event (Active & Headline)
        $headlineEvent = Even::with('sponsors')
            ->where('statusaktif', 1)
            ->where('statusheadline', 1)
            ->latest()
            ->first();

        // 2. Main Active Event for Hero (Headline preferred)
        $activeEvent = $headlineEvent ?: Even::with('sponsors')
            ->where('statusaktif', 1)
            ->latest()
            ->first();

        // 2. Stats
        $stats = [
            'total_vacancies' => $activeEvent ? $activeEvent->lowongans()->count() : 0,
            'total_partners' => $activeEvent ? $activeEvent->registers()->where('aktivasi', 1)->count() : 0,
            'events_count' => Even::count()
        ];

        // 3. Trending/Latest Vacancies (Optional but good for wow factor)
        $vacancies = Lowongan::with(['register.perusahaan', 'register.even'])
            ->whereHas('register', function($q) use ($activeEvent) {
                if ($activeEvent) $q->where('idperiode', $activeEvent->id)->where('aktivasi', 1);
            })
            ->latest()
            ->take(4)
            ->get();

        return view('frontend.home', compact('activeEvent', 'headlineEvent', 'stats', 'vacancies'));
    }

    public function vacancyDetail($id)
    {
        try {
            // Support both encrypted and plain IDs for flexibility
            $decryptedId = null;
            try {
                $decryptedId = \Illuminate\Support\Facades\Crypt::decrypt($id);
            } catch (\Exception $e) {
                $decryptedId = $id;
            }

            $vacancy = Lowongan::with([
                'register.perusahaan.kategori', 
                'register.even.sesis', 
                'kategori',
                'skills.skill',
                'jurusans.jurusan',
            ])->findOrFail($decryptedId);

            $event = $vacancy->register->even;
            $user = auth()->user();
            $applyStats = [
                'has_applied' => false,
                'count_in_event' => 0,
                'limit_reached' => false,
                'global_limit_reached' => false,
                'total_event_applicants' => \App\Models\Lamaran::where('ideven', $event->id)->count()
            ];

            if ($user && $user->idpelamar) {
                $applyStats['applied_vacancy_ids'] = \App\Models\Lamaran::where('idpelamar', $user->idpelamar)
                    ->where('ideven', $event->id)
                    ->pluck('idlowongan')
                    ->toArray();

                $applyStats['count_in_event'] = count($applyStats['applied_vacancy_ids']);
                
                $applyStats['has_applied_this'] = in_array($vacancy->id, $applyStats['applied_vacancy_ids']);

                $applyStats['is_wishlisted'] = \App\Models\Wishlist::where('idpelamar', $user->idpelamar)
                    ->where('idlowongan', $vacancy->id)
                    ->exists();

                if ($event->maksimum_apply > 0 && $applyStats['count_in_event'] >= $event->maksimum_apply) {
                    $applyStats['limit_reached'] = true;
                }
            } else {
                $applyStats['applied_vacancy_ids'] = [];
                $applyStats['is_wishlisted'] = false;
            }

            if ($event->kuota_maksimum > 0 && $applyStats['total_event_applicants'] >= $event->kuota_maksimum) {
                $applyStats['global_limit_reached'] = true;
            }

            $eventVacancies = Lowongan::with(['register.perusahaan', 'kategori'])
                ->whereHas('register', function($q) use ($vacancy) {
                    $q->where('idperiode', $vacancy->register->idperiode)->where('aktivasi', 1);
                })
                ->get();

            return view('frontend.vacancy_detail', compact('vacancy', 'eventVacancies', 'applyStats'));
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Lowongan tidak ditemukan.');
        }
    }

    public function events(Request $request)
    {
        if (auth()->check() && auth()->user()->hasRole('Pelamar') && !auth()->user()->idpelamar) {
            return redirect()->route('pelamar.complete-data')->with('warning', 'Silakan lengkapi profil Anda terlebih dahulu.');
        }

        $q = $request->q;

        $events = Even::withCount('lowongans')
            ->when($q, function($query) use ($q) {
                $query->where('namaperiode', 'like', '%' . $q . '%')
                      ->orWhere('lokasi', 'like', '%' . $q . '%');
            })
            ->orderBy('statusaktif', 'desc')
            ->orderByRaw('CASE WHEN tanggalawal >= CURRENT_DATE THEN 0 ELSE 1 END ASC')
            ->orderByRaw('ABS(DATEDIFF(tanggalawal, CURRENT_DATE)) ASC')
            ->paginate(12)
            ->withQueryString();

        return view('frontend.events', compact('events', 'q'));
    }

    public function eventVacancies(\Illuminate\Http\Request $request, $id)
    {
        try {
            $decryptedId = null;
            try {
                $decryptedId = \Illuminate\Support\Facades\Crypt::decrypt($id);
            } catch (\Exception $e) {
                // Support plain ID for backward compatibility if needed, but primary is encrypted
                $decryptedId = $id;
            }

            if (auth()->check() && auth()->user()->hasRole('Pelamar') && !auth()->user()->idpelamar) {
                 return redirect()->route('pelamar.complete-data')->with('warning', 'Silakan lengkapi profil Anda terlebih dahulu.');
            }

            $event = Even::findOrFail($decryptedId);
            
            $query = Lowongan::with(['register.perusahaan', 'kategori'])
                ->whereHas('register', function($q) use ($decryptedId) {
                    $q->where('idperiode', $decryptedId)->where('aktivasi', 1);
                });
                
            // Industrial Filtering Suite
            if ($request->filled('lokasi')) {
                $query->where('kategorilokasi', $request->lokasi);
            }
            if ($request->filled('perusahaan')) {
                $query->whereHas('register.perusahaan', function($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->perusahaan . '%');
                });
            }
            if ($request->filled('kategori')) {
                $query->where('idkategori', $request->kategori);
            }
            if ($request->filled('gaji_max')) {
                $query->where('gaji_awal', '<=', $request->gaji_max);
            }
            
            $vacancies = $query->latest()->paginate(20)->withQueryString();
            
            $events = Even::where('statusaktif', 1)->orderBy('tanggalawal', 'desc')->get();
            $categories = \App\Models\KategoriLowongan::all();
            $locations = Lowongan::distinct()->pluck('kategorilokasi');

            return view('frontend.event_vacancies', compact('event', 'events', 'vacancies', 'categories', 'locations'));
        } catch (\Exception $e) {
            return redirect()->route('frontend.events')->with('error', 'Event tidak ditemukan.');
        }
    }
}

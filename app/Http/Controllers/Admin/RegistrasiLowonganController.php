<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lamaran;
use App\Models\Even;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\Auth;

class RegistrasiLowonganController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdminEvent = $user->hasRole('Admin Event');

        $idperiode = $request->idperiode;
        $idperusahaan = $request->idperusahaan;
        $idlowongan = $request->idlowongan;
        $q = $request->q;

        if ($isAdminEvent) {
            $idperiode = $user->ideven;
            $events = Even::where('id', $idperiode)->get();
            $companies = Perusahaan::whereHas('registers', function($q) use ($idperiode) {
                $q->where('idperiode', $idperiode);
            })->get();
            $vacancies = Lowongan::whereHas('register', function($q) use ($idperiode) {
                $q->where('idperiode', $idperiode);
            })->get();
        } else {
            $events = Even::orderBy('statusaktif', 'desc')->get();
            $companies = Perusahaan::orderBy('nama')->get();
            $vacancies = Lowongan::orderBy('namalowongan')->get();
        }

        $query = Lamaran::with(['pelamar', 'lowongan.register.perusahaan', 'lowongan.register.even', 'sesi']);

        if ($idperiode) {
            $query->whereHas('lowongan.register', function($q) use ($idperiode) {
                $q->where('idperiode', $idperiode);
            });
        }
        
        if ($idperusahaan) {
            $query->whereHas('lowongan.register', function($q) use ($idperusahaan) {
                $q->where('idperusahaan', $idperusahaan);
            });
        }

        if ($idlowongan) {
            $query->where('idlowongan', $idlowongan);
        }

        if ($q) {
            $query->whereHas('pelamar', function($pq) use ($q) {
                $pq->where('namalengkap', 'LIKE', "%{$q}%")
                   ->orWhere('noktp', 'LIKE', "%{$q}%");
            });
        }

        $lamarans = $query->latest()->paginate(15);

        return view('admin.registrasi_lowongan.index', compact(
            'lamarans', 'events', 'companies', 'vacancies',
            'idperiode', 'idperusahaan', 'idlowongan', 'q', 'isAdminEvent'
        ));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\Even;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelamarEventController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdminEvent = $user->hasRole('Admin Event');
        $isAdminPerusahaan = $user->hasRole('Admin Perusahaan');

        if ($isAdminEvent) {
            // Admin Event: paksa scope ke event yang dikelolanya
            $idperiode = $user->ideven;
            $events = Even::where('id', $idperiode)->get();
        } elseif ($isAdminPerusahaan) {
            // Admin Perusahaan: hanya event yang dia buat sendiri
            $events = Even::where('useradd', $user->id)
                ->orderBy('tanggalawal', 'desc')
                ->get();

            $idperiode = $request->idperiode;

            // Guard: kalo idperiode yg diminta bukan miliknya, abaikan
            if ($idperiode && !$events->contains('id', $idperiode)) {
                $idperiode = null;
            }
        } else {
            // Superadmin & role lain: bebas, semua event
            $idperiode = $request->idperiode;
            $events = Even::select('id', 'namaperiode')->get();
        }

        $q = $request->q;

        $query = Lamaran::with(['pelamar.user', 'lowongan.register.perusahaan', 'lowongan.register.even']);

        if ($idperiode) {
            $query->whereHas('lowongan.register', function ($rq) use ($idperiode) {
                $rq->where('idperiode', $idperiode);
            });
        } elseif ($isAdminPerusahaan) {
            // Kalo gak pilih event spesifik, tetep dibatasin cuma event2 miliknya
            $query->whereHas('lowongan.register', function ($rq) use ($events) {
                $rq->whereIn('idperiode', $events->pluck('id'));
            });
        }

        if ($q) {
            $query->whereHas('pelamar', function ($pq) use ($q) {
                $pq->where('namalengkap', 'LIKE', "%$q%");
            });
        }

        $lamarans = $query->latest()->paginate(15);

        return view('admin.pelamar_event.index', compact('lamarans', 'q', 'events', 'idperiode', 'isAdminEvent', 'isAdminPerusahaan'));
    }
}

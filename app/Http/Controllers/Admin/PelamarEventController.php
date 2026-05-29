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

        // Jika Admin Event, paksa scope ke event yang dikelolanya
        if ($isAdminEvent) {
            $idperiode = $user->ideven;
            $events = Even::where('id', $idperiode)->get();
        } else {
            $idperiode = $request->idperiode;
            $events = Even::select('id', 'namaperiode')->get();
        }

        $q = $request->q;

        $query = Lamaran::with(['pelamar.user', 'lowongan.register.perusahaan', 'lowongan.register.even']);

        if ($idperiode) {
            $query->whereHas('lowongan.register', function ($rq) use ($idperiode) {
                $rq->where('idperiode', $idperiode);
            });
        }

        if ($q) {
            $query->whereHas('pelamar', function ($pq) use ($q) {
                $pq->where('namalengkap', 'LIKE', "%$q%");
            });
        }

        $lamarans = $query->latest()->paginate(15);

        return view('admin.pelamar_event.index', compact('lamarans', 'q', 'events', 'idperiode', 'isAdminEvent'));
    }
}

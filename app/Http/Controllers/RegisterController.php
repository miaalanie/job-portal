<?php

namespace App\Http\Controllers;

use App\Models\Even;
use App\Models\Register;
use App\Models\Perusahaan;
use App\Models\Paket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function index(Request $request)
    {
        $idperiode = $request->idperiode;
        $query = Register::with(['perusahaan', 'even', 'payment']);

        if ($idperiode) {
            $query->where('idperiode', $idperiode);
        }

        $events = Even::all();
        $registrations = $query->latest()->get();

        return view('admin.register.index', compact('registrations', 'events', 'idperiode'));
    }

    public function create(Request $request)
    {
        $idperiode = $request->idperiode;
        $events = Even::all();
        $perusahaans = Perusahaan::all();
        $pakets = Paket::all();

        return view('admin.register.create', compact('events', 'perusahaans', 'pakets', 'idperiode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idperusahaan' => 'required|exists:perusahaans,id',
            'idperiode' => 'required|exists:evens,id',
            'namapaket' => 'required|string',
            'tanggalregister' => 'required|date',
        ]);

        $data = $request->all();
        $data['useradd'] = Auth::id();
        $data['aktivasi'] = $request->has('aktivasi');

        Register::create($data);

        return redirect()->route('admin.register', ['idperiode' => $request->idperiode])
                         ->with('success', 'Pendaftaran perusahaan berhasil ditambahkan.');
    }

    public function toggleAktivasi($id)
    {
        $register = Register::findOrFail($id);
        $register->aktivasi = !$register->aktivasi;
        $register->userupdate = Auth::id();
        $register->save();

        return back()->with('success', 'Status aktivasi berhasil diubah.');
    }

    public function destroy($id)
    {
        $register = Register::findOrFail($id);
        $register->delete();

        return back()->with('success', 'Pendaftaran perusahaan berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Models\Even;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class LowonganKerjaController extends Controller
{
    /**
     * Display a listing of job vacancies with active event sorting and filters.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdminEvent = $user->hasRole('Admin Event');

        $query = Lowongan::query()
            ->join('registers', 'lowongans.idregister', '=', 'registers.id')
            ->join('evens', 'registers.idperiode', '=', 'evens.id')
            ->join('perusahaans', 'registers.idperusahaan', '=', 'perusahaans.id')
            ->with(['register.even', 'register.perusahaan', 'kategori'])
            ->withCount('lamarans')
            ->select('lowongans.*');

        // Filtering
        if ($isAdminEvent) {
            $ideven = $user->ideven;
            $query->where('registers.idperiode', $ideven);
            $events = Even::where('id', $ideven)->get();
            $companies = Perusahaan::whereHas('registers', function($q) use ($ideven) {
                $q->where('idperiode', $ideven);
            })->orderBy('nama')->get();
        } else {
            if ($request->filled('even')) {
                $query->where('registers.idperiode', $request->even);
            }
            $events = Even::orderBy('statusaktif', 'desc')->latest()->get();
            $companies = Perusahaan::orderBy('nama')->get();
        }

        if ($request->filled('perusahaan')) {
            $query->where('registers.idperusahaan', $request->perusahaan);
        }

        // Sorting by Active Event First
        $vacancies = $query->orderBy('evens.statusaktif', 'desc')
            ->orderBy('lowongans.created_at', 'desc')
            ->get();

        return view('admin.lowongan_kerja.index', compact('vacancies', 'events', 'companies', 'isAdminEvent'));
    }

    /**
     * Display the specified vacancy audit profile and applicant list.
     */
    public function show($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $vacancy = Lowongan::with([
                'register.even', 
                'register.perusahaan', 
                'kategori', 
                'lamarans.pelamar'
            ])->findOrFail($decryptedId);

            return view('admin.lowongan_kerja.show', compact('vacancy'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data lowongan tidak ditemukan atau ID tidak valid.');
        }
    }
}

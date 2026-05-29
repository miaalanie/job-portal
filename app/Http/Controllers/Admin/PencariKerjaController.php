<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelamar;
use App\Models\Provinsi;
use App\Models\Kota;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Exports\ApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\DirectTalentMail;

class PencariKerjaController extends Controller
{
    /**
     * Display a listing of job seekers with geographic filters.
     */
    public function index(Request $request)
    {
        $query = Pelamar::query()->withCount('lamarans')->with(['kelurahan.kecamatan.kota.provinsi']);

        // Applying Geographic Filters
        if ($request->filled('provinsi')) {
            $query->whereHas('kelurahan.kecamatan.kota', function ($q) use ($request) {
                $q->where('idprovinsi', $request->provinsi);
            });
        }
        if ($request->filled('kota')) {
            $query->whereHas('kelurahan.kecamatan', function ($q) use ($request) {
                $q->where('idkota', $request->kota);
            });
        }
        if ($request->filled('kecamatan')) {
            $query->whereHas('kelurahan', function ($q) use ($request) {
                $q->where('idkecamatan', $request->kecamatan);
            });
        }
        if ($request->filled('kelurahan')) {
            $query->where('idkelurahan', $request->kelurahan);
        }

        $applicants = $query->latest()->get();
        $provinces = Provinsi::orderBy('nama')->get();
        
        return view('admin.pencari_kerja.index', compact('applicants', 'provinces'));
    }

    /**
     * Display the specified talent audit profile.
     */
    public function show($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $applicant = Pelamar::with([
                'user',
                'pendidikans', 
                'pengalamans', 
                'dokumens', 
                'skills',
                'lamarans.lowongan.register.even'
            ])->findOrFail($decryptedId);

            return view('admin.pencari_kerja.show', compact('applicant'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data pelamar tidak ditemukan atau ID tidak valid.');
        }
    }

    /**
     * Generate and download Applicant CV as PDF.
     */
    public function downloadCV($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $applicant = Pelamar::with([
                'user', 'pendidikans', 'pengalamans', 'dokumens', 'skills'
            ])->findOrFail($decryptedId);

            $pdf = Pdf::loadView('admin.pencari_kerja.cv_pdf', compact('applicant'));
            return $pdf->download('CV_' . str_replace(' ', '_', $applicant->namalengkap) . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses CV: ' . $e->getMessage());
        }
    }

    /**
     * Send direct email to Talent.
     */
    public function sendMail(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $decryptedId = Crypt::decrypt($id);
            $applicant = Pelamar::with('user')->findOrFail($decryptedId);

            if (!$applicant->user || !$applicant->user->email) {
                return response()->json(['status' => 'error', 'message' => 'Email pelamar tidak ditemukan.'], 422);
            }

            Mail::to($applicant->user->email)->send(new DirectTalentMail($request->subject, $request->message, $applicant));

            return response()->json([
                'status' => 'success',
                'message' => 'Pesan berhasil dikirim ke ' . $applicant->namalengkap
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengirim email: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export talent data to Excel based on current filters.
     */
    public function export(Request $request)
    {
        $filters = $request->only(['provinsi', 'kota', 'kecamatan', 'kelurahan']);
        return Excel::download(new ApplicantsExport($filters), 'Data_Pencari_Kerja_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * AJAX endpoints for dependent dropdowns.
     */
    public function getCities($provinceId)
    {
        return response()->json(Kota::where('idprovinsi', $provinceId)->orderBy('nama')->get());
    }

    public function getDistricts($cityId)
    {
        return response()->json(Kecamatan::where('idkota', $cityId)->orderBy('nama')->get());
    }

    public function getVillages($districtId)
    {
        return response()->json(Kelurahan::where('idkecamatan', $districtId)->orderBy('nama')->get());
    }
}

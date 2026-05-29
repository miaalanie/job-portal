<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Even;
use App\Models\Lowongan;
use App\Models\Lamaran;
use App\Models\User;
use App\Models\Pelamar;
use App\Models\Register;
use App\Mail\CompanyApprovedMail;
use App\Mail\CompanyRejectedMail;
use App\Mail\CompanyEventApprovedMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirect Admin Perusahaan to their dedicated dashboard
        if ($user->hasRole('Admin Perusahaan')) {
            return redirect()->route('admin.perusahaan.dashboard');
        }

        // Stats for Superadmin & Admin Aplikasi
        $stats = [
            'totalEvents' => Even::count(),
            'activeEvents' => Even::where('statusaktif', 1)->count(),
            'inactiveEvents' => Even::where('statusaktif', 0)->count(),
            'validatedCompanies' => User::role('Admin Perusahaan')->where('statusaktif', 1)->where('statusvalidasi', 1)->count(),
            'totalApplicants' => Pelamar::count(),
            'pendingEventRegistrations' => Register::where('aktivasi', 0)->count(),
        ];

        // Pending Validations (Account Verification)
        $pendingCompanies = User::role('Admin Perusahaan')
            ->where('statusvalidasi', 0)
            ->with('perusahaan')
            ->latest()
            ->get();

        // Pending Event Registrations (Payment/Enrollment Verification)
        $pendingEventRegistrations = Register::where('aktivasi', 0)
            ->with(['perusahaan', 'even', 'payment'])
            ->latest()
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingCompanies', 'pendingEventRegistrations'));
    }


    public function showValidationDetail($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $userVisible = User::with(['perusahaan.dokumen'])->findOrFail($decryptedId);
            return view('admin.perusahaan.validation_detail', compact('userVisible'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'ID tidak valid atau telah dimodifikasi.');
        }
    }

    public function validateCompany(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $user = User::findOrFail($decryptedId);

            // Perusahaan MUST activate email first (statusaktif = 1)
            if ($user->statusaktif == 0) {
                $msg = 'Akun perusahaan belum diaktivasi via email. Validasi hanya dapat dilakukan setelah email terverifikasi.';
                if ($request->ajax()) {
                    return response()->json(['status' => 'error', 'message' => $msg], 422);
                }
                return redirect()->back()->with('error', $msg);
            }

            $user->statusvalidasi = 1;
            $user->save();

            try {
                Mail::to($user->email)->send(new CompanyApprovedMail($user));
            } catch (\Exception $e) {
                // Silently fail if mail settings are incorrect during dev
            }

            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Akun perusahaan berhasil divalidasi.']);
            }
            return redirect()->route('admin.dashboard')->with('success', 'Akun perusahaan berhasil divalidasi.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal memproses validasi.'], 500);
            }
            return redirect()->route('admin.dashboard')->with('error', 'Gagal memproses validasi.');
        }
    }

    public function rejectCompany(Request $request, $id)
    {
        try {
            $request->validate(['reason' => 'required']);
            $decryptedId = Crypt::decrypt($id);
            $user = User::findOrFail($decryptedId);
            
            try {
                Mail::to($user->email)->send(new CompanyRejectedMail($user, $request->reason));
            } catch (\Exception $e) {
                // Silently fail if mail settings are incorrect during dev
            }

            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Validasi perusahaan ditolak dan email dikirim.']);
            }
            return redirect()->route('admin.dashboard')->with('success', 'Validasi perusahaan ditolak.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal memproses penolakan.'], 500);
            }
            return redirect()->route('admin.dashboard')->with('error', 'Gagal memproses penolakan.');
        }
    }
}

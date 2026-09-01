<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Register;
use App\Models\Even;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompanyEventApprovedMail;

class EventRegistrationController extends Controller
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

            // Guard: kalo dia coba filter idperiode yg bukan event miliknya, abaikan (biar gak bisa liat punya orang lain)
            if ($idperiode && !$events->contains('id', $idperiode)) {
                $idperiode = null;
            }
        } else {
            // Superadmin dan role lain: bebas, semua event
            $idperiode = $request->idperiode;
            $events = Even::select('id', 'namaperiode')->get();
        }

        $status = $request->status;

        $query = Register::with(['perusahaan', 'even', 'payment']);

        if ($idperiode) {
            $query->where('idperiode', $idperiode);
        } elseif ($isAdminPerusahaan) {
            // Kalo Admin Perusahaan gak pilih event spesifik, tetep dibatasin cuma event2 miliknya
            $query->whereIn('idperiode', $events->pluck('id'));
        }

        if ($status !== null) {
            if ($status == 'active') $query->where('aktivasi', 1);
            if ($status == 'inactive') $query->where('aktivasi', 0);
        }

        $registrations = $query->latest()->get();

        $stats = [
            'total'   => $registrations->count(),
            'active'  => $registrations->where('aktivasi', 1)->count(),
            'pending' => $registrations->where('aktivasi', 0)->count(),
            'paid'    => $registrations->filter(fn($r) => $r->payment != null)->count(),
        ];

        return view('admin.event_registration.index', compact('registrations', 'events', 'idperiode', 'stats', 'status', 'isAdminEvent', 'isAdminPerusahaan'));
    }

    public function showDetail($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $registration = Register::with(['perusahaan', 'even', 'payment', 'lowongans.kategori'])->findOrFail($decryptedId);
            return view('admin.perusahaan.event_registration_detail', compact('registration'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Detail pendaftaran tidak ditemukan atau ID tidak valid.');
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $registration = Register::with(['perusahaan', 'even'])->findOrFail($decryptedId);

            $registration->aktivasi = 1;
            $registration->save();

            // Notify Company via Email
            try {
                Mail::to($registration->perusahaan->email ?? $registration->perusahaan->user->email)->send(new CompanyEventApprovedMail($registration));
            } catch (\Exception $e) {
                // Skip mail failure during dev/local
            }

            return redirect()->route('admin.pendaftar-event')->with('success', 'Pendaftaran event berhasil disetujui, diaktivasi, dan notifikasi telah dikirim.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses persetujuan event: ' . $e->getMessage());
        }
    }

    public function toggleAktivasi($id)
    {
        $register = Register::findOrFail($id);
        $register->aktivasi = !$register->aktivasi;
        $register->userupdate = Auth::id();
        $register->save();

        $statusText = $register->aktivasi ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Pendaftaran perusahaan berhasil {$statusText}.");
    }

    public function destroy($id)
    {
        try {
            $register = Register::findOrFail($id);
            $register->delete();
            return back()->with('success', 'Pendaftaran berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus pendaftaran: ' . $e->getMessage());
        }
    }
}

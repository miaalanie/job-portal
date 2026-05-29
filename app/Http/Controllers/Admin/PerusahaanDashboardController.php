<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Register;
use App\Models\Even;
use App\Models\Lowongan;
use App\Models\Lamaran;
use App\Models\Paket;
use App\Mail\CompanyEventRegisteredMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\PengaturanPerusahaan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PerusahaanDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasRole('Admin Perusahaan')) {
            return redirect()->route('admin.dashboard');
        }

        $idperusahaan = $user->idperusahaan;
        $isValidated = $user->statusvalidasi == 1;

        $dashboardData = [
            'totalLowongan' => 0,
            'totalPelamar' => 0,
            'activeEvents' => collect(),
            'registeredEvents' => Register::with(['even', 'payment'])->where('idperusahaan', $idperusahaan)->latest()->get(),
            'registeredEventIds' => Register::where('idperusahaan', $idperusahaan)->pluck('idperiode')->toArray(),
            'isValidated' => $isValidated,
            'companyName' => $user->perusahaan->nama ?? 'Perusahaan'
        ];

        if ($isValidated) {
            // Active Events (Not yet registered and sorted by closest start date)
            $dashboardData['activeEvents'] = Even::where('statusaktif', 1)
                ->orderBy('tanggalawal', 'asc')
                ->get();
            
            // Total Vacancies for this company (in events where they are registered and active)
            $dashboardData['totalLowongan'] = Lowongan::whereHas('register', function($q) use ($idperusahaan) {
                $q->where('idperusahaan', $idperusahaan);
            })->count();
            
            // Total Applicants
            $dashboardData['totalPelamar'] = Lamaran::whereHas('lowongan', function($q) use ($idperusahaan) {
                $q->whereHas('register', function($q2) use ($idperusahaan) {
                    $q2->where('idperusahaan', $idperusahaan);
                });
            })->count();
        }

        return view('admin.perusahaan.dashboard', compact('dashboardData'));
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'idregister' => 'required|exists:registers,id',
            'bank_asal' => 'required|string',
            'nama_pengirim' => 'required|string',
            'jumlah_bayar' => 'required|numeric',
            'tanggal_bayar' => 'required|date',
            'bukti_bayar' => 'required|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        try {
            $register = Register::findOrFail($request->idregister);
            
            // Upload Proof
            $path = $request->file('bukti_bayar')->store('payments', 'public');

            \App\Models\RegisterPayment::create([
                'idregister' => $request->idregister,
                'bank_asal' => $request->bank_asal,
                'nama_pengirim' => $request->nama_pengirim,
                'jumlah_bayar' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
                'bukti_bayar' => $path,
                'status' => 'Menunggu Verifikasi',
                'catatan' => $request->catatan,
            ]);

            // Notify Admin
            DB::table('system_notifications')->insert([
                'type' => 'payment_confirmation',
                'title' => 'Konfirmasi Pembayaran Baru',
                'message' => 'Perusahaan "' . (Auth::user()->perusahaan->nama ?? 'Unknown') . '" telah mengirimkan bukti bayar.',
                'url' => route('admin.register'), // Or specialized payment verification page
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Konfirmasi pembayaran berhasil dikirim. Admin akan segera melakukan verifikasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses konfirmasi pembayaran: ' . $e->getMessage());
        }
    }

    public function downloadInvoice($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $register = Register::with(['even', 'perusahaan', 'payment'])->findOrFail($decryptedId);

            // Security check: ensure this invoice belongs to the logged-in company
            if ($register->idperusahaan != Auth::user()->idperusahaan) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }

            $pengaturan = PengaturanPerusahaan::first();
            
            $data = [
                'register' => $register,
                'even' => $register->even,
                'perusahaan' => $register->perusahaan,
                'pengaturan' => $pengaturan,
                'invoice_no' => 'INV-' . str_pad($register->id, 5, '0', STR_PAD_LEFT) . '/' . Carbon::parse($register->tanggalregister)->format('Y'),
            ];

            $safeFilename = str_replace('/', '-', $data['invoice_no']);
            $pdf = Pdf::loadView('admin.perusahaan.invoice_pdf', $data);
            
            return $pdf->download('Invoice-' . $safeFilename . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghasilkan invoice: ' . $e->getMessage());
        }
    }

    public function eventDetail($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $even = Even::with(['pakets', 'sponsors'])->where('statusaktif', 1)->findOrFail($decryptedId);
            
            // Check if already registered
            $isRegistered = Register::where('idperusahaan', Auth::user()->idperusahaan)
                                   ->where('idperiode', $decryptedId)
                                   ->exists();

            return view('admin.perusahaan.event_detail', compact('even', 'isRegistered'));
        } catch (\Exception $e) {
            return redirect()->route('admin.perusahaan.dashboard')->with('error', 'Event tidak ditemukan.');
        }
    }

    public function registerEvent(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $even = Even::findOrFail($decryptedId);
            
            // Validation
            if ($even->statuspaket == 1) {
                $request->validate([
                    'idpaket' => 'required|exists:even_pakets,id',
                    'terms' => 'accepted'
                ], [
                    'idpaket.required' => 'Silakan pilih paket keikutsertaan.',
                    'terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.'
                ]);
            } else {
                $request->validate([
                    'terms' => 'accepted'
                ], [
                    'terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.'
                ]);
            }

            $user = Auth::user();
            
            // Check if already registered
            $exists = Register::where('idperusahaan', $user->idperusahaan)
                             ->where('idperiode', $decryptedId)
                             ->exists();
            
            if ($exists) {
                return redirect()->back()->with('error', 'Anda sudah terdaftar di event ini.');
            }

            $namapaket = "0";
            $biaya = $even->biaya ?? 0;

            if ($even->statuspaket == 1) {
                $paket = \App\Models\EvenPaket::findOrFail($request->idpaket);
                $namapaket = $paket->id;
                $biaya = $paket->harga;
            }

            $register = Register::create([
                'idperusahaan' => $user->idperusahaan,
                'idperiode' => $decryptedId,
                'namapaket' => $namapaket,
                'biaya' => $biaya,
                'tanggalregister' => Carbon::now(),
                'aktivasi' => 0, 
                'useradd' => $user->id
            ]);

            // Send notification to Admin Aplikasi
            DB::table('system_notifications')->insert([
                'type' => 'event_registration',
                'title' => 'Pendaftaran Event Baru',
                'message' => 'Perusahaan "' . ($user->perusahaan->nama ?? 'Unknown') . '" mendaftar ke event "' . $even->namaperiode . '".',
                'url' => route('admin.register'),
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Load relations for email
            $register->load(['perusahaan', 'even']);

            try {
                Mail::to($user->email)->send(new CompanyEventRegisteredMail($register));
            } catch (\Exception $e) {
                // Ignore mail fail in local
            }

            return redirect()->route('admin.perusahaan.dashboard')->with('success', 'Pendaftaran berhasil! Silakan cek email Anda untuk instruksi pembayaran.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mendaftar event: ' . $e->getMessage());
        }
    }

    public function myEvents()
    {
        $idperusahaan = Auth::user()->idperusahaan;
        $registrations = Register::with(['even', 'payment'])
            ->withCount('lowongans')
            ->where('idperusahaan', $idperusahaan)
            ->latest()
            ->get();

        return view('admin.perusahaan.my_events', compact('registrations'));
    }

    public function myEventDetail($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $registration = Register::with(['even', 'perusahaan', 'payment'])
                ->findOrFail($decryptedId);

            // Security check
            if ($registration->idperusahaan != Auth::user()->idperusahaan) {
                abort(403);
            }

            // Fetch vacancies specifically posted for this event registration
            $lowongans = Lowongan::where('idregister', $decryptedId)
                ->with('kategori')
                ->withCount('lamarans')
                ->get();

            return view('admin.perusahaan.my_event_detail', compact('registration', 'lowongans'));
        } catch (\Exception $e) {
            return redirect()->route('admin.perusahaan.event')->with('error', 'Detail event tidak ditemukan.');
        }
    }
}

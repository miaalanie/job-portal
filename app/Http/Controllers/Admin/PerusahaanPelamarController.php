<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\Pelamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\DirectTalentMail;

class PerusahaanPelamarController extends Controller
{
    public function index(Request $request)
    {
        $idperusahaan = Auth::user()->idperusahaan;
        $q = $request->q;
        $idperiode = $request->idperiode;
        $idlowongan = $request->idlowongan;

        // Fetch company's related filters
        $events = \App\Models\Even::whereHas('registers', function($query) use ($idperusahaan) {
            $query->where('idperusahaan', $idperusahaan);
        })->get();

        $lowongans_list = \App\Models\Lowongan::whereHas('register', function($query) use ($idperusahaan) {
            $query->where('idperusahaan', $idperusahaan);
        })->get();

        $lamarans = Lamaran::with(['pelamar', 'lowongan.register.even'])
            ->whereHas('lowongan.register', function($query) use ($idperusahaan) {
                $query->where('idperusahaan', $idperusahaan);
            })
            ->when($idperiode, function($query) use ($idperiode) {
                $query->whereHas('lowongan.register', function($rq) use ($idperiode) {
                    $rq->where('idperiode', $idperiode);
                });
            })
            ->when($idlowongan, function($query) use ($idlowongan) {
                $query->where('idlowongan', $idlowongan);
            })
            ->when($q, function($query) use ($q) {
                $query->whereHas('pelamar', function($pq) use ($q) {
                    $pq->where('namalengkap', 'LIKE', "%$q%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.perusahaan.pelamar_index', compact('lamarans', 'q', 'events', 'lowongans_list', 'idperiode', 'idlowongan'));
    }

    /**
     * Show detailed applicant profile for Company Admin.
     */
    public function show($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $applicant = Pelamar::with([
                'user', 'pendidikans', 'pengalamans', 'dokumens', 'skills', 'lamarans.lowongan.register.even'
            ])->findOrFail($decryptedId);

            return view('admin.pencari_kerja.show', compact('applicant'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data pelamar tidak ditemukan atau ID tidak valid.');
        }
    }

    /**
     * Download CV for Company Admin.
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
     * Send direct email to Talent from Company Admin.
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

    public function export(Request $request)
    {
        $idperusahaan = Auth::user()->idperusahaan;
        $idperiode = $request->idperiode;
        $idlowongan = $request->idlowongan;

        $lamarans = Lamaran::with(['pelamar', 'lowongan.register.even', 'lowongan.kategori'])
            ->whereHas('lowongan.register', function($query) use ($idperusahaan) {
                $query->where('idperusahaan', $idperusahaan);
            })
            ->when($idperiode, function($query) use ($idperiode) {
                $query->whereHas('lowongan.register', function($rq) use ($idperiode) {
                    $rq->where('idperiode', $idperiode);
                });
            })
            ->when($idlowongan, function($query) use ($idlowongan) {
                $query->where('idlowongan', $idlowongan);
            })
            ->get();

        $filename = "data_pelamar_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Nama Lengkap', 'Lowongan', 'Kategori', 'Event', 'Placement', 'KTP', 'Jns Kelamin', 'Alamat', 'Tgl Datang', 'Tgl Melamar', 'Status'];

        $callback = function() use($lamarans, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($lamarans as $key => $lamaran) {
                $row = [
                    $key + 1,
                    $lamaran->pelamar->namalengkap ?? '-',
                    $lamaran->lowongan->namalowongan ?? '-',
                    $lamaran->lowongan->kategori->nama ?? '-',
                    $lamaran->lowongan->register->even->namaperiode ?? '-',
                    $lamaran->lowongan->kategorilokasi ?? '-',
                    $lamaran->pelamar->noktp ?? '-',
                    $lamaran->pelamar->jeniskelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                    $lamaran->pelamar->alamatlengkap ?? '-',
                    $lamaran->tanggal_datang ? \Carbon\Carbon::parse($lamaran->tanggal_datang)->format('d/m/Y') : '-',
                    $lamaran->created_at->format('d/m/Y H:i'),
                    $lamaran->statusditerima == 1 ? 'Lolos' : ($lamaran->statusditerima == 2 ? 'Gagal' : 'Proses')
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
